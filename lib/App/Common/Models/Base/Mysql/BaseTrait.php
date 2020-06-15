<?php

namespace App\Common\Models\Base\Mysql;

trait BaseTrait
{

    protected function getMongoId4Query($_id)
    {
        if (is_array($_id)) {
            $list = array();
            foreach ($_id as $item) {
                if ($item instanceof \MongoId) {
                    $list[] = $item->__toString();
                } else {
                    $list[] = $item;
                }
            }
            return $list;
        } else {
            if ($_id instanceof \MongoId) {
                return $_id->__toString();
            } else {
                return $_id;
            }
        }
    }

    protected function getConditions(array $where, $condition_op = "AND", $level = 0)
    {
        $unique = uniqid();
        $conditions = array();
        $bind = array();
        $forUpdate = array();

        // 0级条件的时候才会有以下的判断
        if ($level <= 0) {
            // 如果没有这个条件那么增加这个条件
            if (!array_key_exists("__REMOVED__", $where)) {
                $where["__REMOVED__"] = 0;
            }

            // 如果__FOR_UPDATE__ 存在的话
            if (array_key_exists("__FOR_UPDATE__", $where)) {
                $forUpdate["for_update"] = $where["__FOR_UPDATE__"];
                unset($where["__FOR_UPDATE__"]);
            }
        }


        // 如果__QUERY_OR__ 存在的话
        if (array_key_exists("__QUERY_OR__", $where)) {
            $condition_op = "OR";
            $orConditions = $where["__QUERY_OR__"];
            unset($where["__QUERY_OR__"]);
            $bind = array();
            $conditions = array();
            foreach ($orConditions as $condition) {
                $query = $this->getConditions($condition, "AND", $level + 1);
                $bind = array_merge($bind, $query['bind']);
                $conditions[] = $query['conditions'];
            }
            $where = array();
        }

        foreach ($where as $key => $item) {
            if ($key == '__OR__') {
                // 解决OR查询
                $orConditions = $this->getConditions($item, "OR", $level + 1);
                if (!empty($orConditions)) {
                    $conditions[] = $orConditions['conditions'];
                    $bind = array_merge($bind, $orConditions['bind']);
                }
            } elseif ($key == '$exp') {
                // 解决直接rawsql的问题
                if (is_array($item)) {
                    foreach ($item as $value) {
                        $conditions[] = $value;
                    }
                } else {
                    $conditions[] = $item;
                }
            } else {
                $fieldKey = "[{$key}]";
                $bindKey = "__{$key}{$unique}__";
                if (is_array($item)) {
                    foreach ($item as $op => $value) {
                        $value = $this->changeValue4Conditions($value, $key);
                        if ($op == '$in') {
                            if (!empty($value)) {
                                // $conditions[] = "{$fieldKey} IN ({{$bindKey}:array})";
                                // $bind[$bindKey] = array_values($value);
                                $bindKey4In = array();
                                foreach (array_values($value) as $idex => $item) {
                                    $bindKey4In[] = $bindKey . '_' . $idex;
                                    $bind[$bindKey . '_' . $idex] = $item;
                                }
                                $bindKey4In = implode(':,:', $bindKey4In);
                                $conditions[] = "{$fieldKey} IN (:{$bindKey4In}:)";
                            } else {
                                $conditions[] = "{$fieldKey}=:{$bindKey}:";
                                $bind[$bindKey] = "";
                            }
                        }
                        if ($op == '$nin') {
                            if (!empty($value)) {
                                // $conditions[] = "{$fieldKey} NOT IN ({{$bindKey}:array})";
                                // $bind[$bindKey] = array_values($value);
                                $bindKey4In = array();
                                foreach (array_values($value) as $idex => $item) {
                                    $bindKey4In[] = $bindKey . '_' . $idex;
                                    $bind[$bindKey . '_' . $idex] = $item;
                                }
                                $bindKey4In = implode(':,:', $bindKey4In);
                                $conditions[] = "{$fieldKey} NOT IN (:{$bindKey4In}:)";
                            } else {
                                $conditions[] = "{$fieldKey}!=:{$bindKey}:";
                                $bind[$bindKey] = "";
                            }
                        }

                        if ($op == '$ne') {
                            $conditions[] = "{$fieldKey}!=:{$bindKey}:";
                            $bind[$bindKey] = $value;
                        }
                        if ($op == '$lt') {
                            $conditions[] = "{$fieldKey}<:lt_{$bindKey}:";
                            $bind['lt_' . $bindKey] = $value;
                        }
                        if ($op == '$lte') {
                            $conditions[] = "{$fieldKey}<=:lte_{$bindKey}:";
                            $bind['lte_' . $bindKey] = $value;
                        }

                        if ($op == '$gt') {
                            $conditions[] = "{$fieldKey}>:gt_{$bindKey}:";
                            $bind['gt_' . $bindKey] = $value;
                        }
                        if ($op == '$gte') {
                            $conditions[] = "{$fieldKey}>=:gte_{$bindKey}:";
                            $bind['gte_' . $bindKey] = $value;
                        }

                        // '$like'=> '%xxx%'
                        if ($op == '$like') {
                            // 解决like查询
                            $conditions[] = "{$fieldKey} LIKE :like_{$bindKey}:";
                            $bind['like_' . $bindKey] = $value;
                        }

                        // '$or'=>array('$gte'=>1,'lte'=>10)
                        if ($op == '$or') {
                            if (empty($value) || !is_array($value)) {
                                throw new \Exception('$or条件查询格式不正确');
                            }
                            $item2 = array($key => $value);
                            // 解决OR查询
                            $orConditions = $this->getConditions($item2, "OR", $level + 1);
                            if (!empty($orConditions)) {
                                $conditions[] = $orConditions['conditions'];
                                $bind = array_merge($bind, $orConditions['bind']);
                            }
                        }
                    }
                } else {
                    if ($item instanceof \MongoRegex) {
                        $conditions[] = "{$fieldKey} LIKE :{$bindKey}:";
                    } else {
                        $conditions[] = "{$fieldKey}=:{$bindKey}:";
                    }
                    $value = $this->changeValue4Conditions($item, $key);
                    $bind[$bindKey] = $value;
                }
            }
        }
        if (empty($bind)) {
            return array();
        } else {
            return array_merge(array(
                'conditions' => '(' . implode(" {$condition_op} ", $conditions) . ')',
                'bind' => $bind
            ), $forUpdate);
        }
    }

    protected function getSort(array $sort)
    {
        $order = array();
        foreach ($sort as $key => $value) {
            if ($key == '__RANDOM__') {
                // 解决随机查询
                $order[] = "rand()";
            } else {
                $fieldKey = "[{$key}]";
                // $fieldKey = "{$key}";
                if (intval($value) > 0) {
                    $order[] = "{$fieldKey} ASC";
                } else {
                    $order[] = "{$fieldKey} DESC";
                }
            }
        }
        $order = implode(',', $order);
        if (empty($order)) {
            return array();
        } else {
            return array(
                'order' => $order
            );
        }
    }

    protected function changeValue4Conditions($value, $field)
    {
        if ($field == '_id') {
            $value = $this->getMongoId4Query($value);
            // die("_id's value:" . $value);
            return $value;
        } else {
            if (is_bool($value)) {
                $value = intval($value);
                return $value;
            }
            if ($value instanceof \MongoDate) {
                $value = date('Y-m-d H:i:s', $value->sec);
                return $value;
            }
            if ($value instanceof \MongoRegex) {
                // /系统管理员/i->'%Art%'
                $value = $value->__toString();
                $value = str_ireplace('/i', '%', $value);
                $value = str_ireplace('/^$', '', $value);
                $value = str_ireplace('/', '%', $value);
                return $value;
            }
        }
        return $value;
    }

    protected function changeValue4Save($value)
    {
        if ($value instanceof \MongoDate) {
            $value = date('Y-m-d H:i:s', $value->sec);
        } elseif (is_bool($value)) {
            $value = intval($value);
        } elseif (is_array($value)) {
            if (!empty($value)) {
                $value = json_encode($value);
            } else {
                $value = "";
            }
        } elseif (is_object($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    protected function getInsertContents(array $datas)
    {
        $fields = array();
        $bindFields = array();
        $values = array();
        if (empty($datas)) {
            throw new \Exception("字段没有定义", -999);
        }
        if (!isset($datas['_id'])) {
            $_id = new \MongoId();
            $datas['_id'] = $_id->__toString();
        }
        $datas['__CREATE_TIME__'] = $datas['__MODIFY_TIME__'] = \App\Common\Utils\Helper::getCurrentTime();
        $datas['__REMOVED__'] = false;

        // 如果在后台进行操作数据表的话
        if (!empty($_SESSION['admin_id'])) {
            $datas['__CREATE_USER_ID__'] = $datas['__MODIFY_USER_ID__'] = $_SESSION['admin_id'];
            $datas['__CREATE_USER_NAME__'] = $datas['__MODIFY_USER_NAME__'] = (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : '');
        }

        foreach ($datas as $field => $value) {
            $fieldKey = "[{$field}]";
            $fields[] = "{$fieldKey}";
            $fieldBindKey = "{$field}_1";
            $bindFields[] = ":{$fieldBindKey}:";
            $values[$fieldBindKey] = $this->changeValue4Save($value);
        }
        if (empty($fields)) {
            throw new \Exception("字段没有定义", -999);
        } else {
            return array(
                'fields' => implode(",", $fields),
                'bindFields' => implode(",", $bindFields),
                'values' => $values,
                '_id' => $datas['_id']
            );
        }
    }

    protected function getUpdateContents(array $object)
    {
        $fields = array();
        $values = array();
        if (empty($object)) {
            throw new \Exception("更新字段没有定义", -999);
        }

        foreach ($object as $key => $items) {
            switch ($key) {
                case '$exp':
                    if (!empty($items)) {
                        foreach ($items as $field => $value) {
                            $fieldKey = "[{$field}]";
                            $fields[] = "{$fieldKey}={$value}";
                        }
                    }
                    break;
                case '$set':
                    if (!empty($items)) {
                        foreach ($items as $field => $value) {
                            $fieldKey = "[{$field}]";
                            $fields[] = "{$fieldKey}=:{$field}:";
                            $values[$field] = $this->changeValue4Save($value);
                        }
                    }
                    break;
                case '$inc':
                    if (!empty($items)) {
                        foreach ($items as $field => $value) {
                            $fieldKey = "[{$field}]";
                            $value = $this->changeValue4Save($value);
                            $fields[] = "{$fieldKey}={$fieldKey}+{$value}";
                        }
                    }
                    break;
                default:
                    throw new \Exception("更新类别没有定义", -999);
            }
        }

        if (empty($fields)) {
            throw new \Exception("更新字段没有定义", -999);
        } else {
            $field = '__MODIFY_TIME__';
            $value = \App\Common\Utils\Helper::getCurrentTime();
            $fieldKey = "[{$field}]";
            $fields[] = "{$fieldKey}=:{$field}:";
            $values[$field] = $this->changeValue4Save($value);

            // 如果在后台进行操作数据表的话
            if (!empty($_SESSION['admin_id'])) {
                $field = '__MODIFY_USER_ID__';
                $value = $_SESSION['admin_id'];
                $fieldKey = "[{$field}]";
                $fields[] = "{$fieldKey}=:{$field}:";
                $values[$field] = $this->changeValue4Save($value);

                $field = '__MODIFY_USER_NAME__';
                $value = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : '';
                $fieldKey = "[{$field}]";
                $fields[] = "{$fieldKey}=:{$field}:";
                $values[$field] = $this->changeValue4Save($value);
            }

            return array(
                'fields' => implode(",", $fields),
                'values' => $values
            );
        }
    }

    protected function changeToBoolean($field)
    {
        if (empty($field)) {
            return false;
        }
        return ($field);
    }

    protected function changeToArray($field)
    {
        if (empty($field)) {
            return array();
        }
        if (is_array($field)) {
            return $field;
        } else {
            return json_decode($field, true);
        }
    }

    protected function changeToMongoDate($field)
    {
        if (empty($field)) {
            return $field;
        }
        if ($field == '0000-00-00 00:00:00') {
            $field = '0001-01-01 00:00:00';
        }
        return \App\Common\Utils\Helper::getCurrentTime(strtotime($field));
        // if (is_date($field)) {
        // } else {
        // return json_decode($field, true);
        // }
    }

    protected function getColumns(array $fields = array())
    {
        $ret = array();
        if (!empty($fields)) {
            $ret['column'] = implode(',', $fields);
        }
        return $ret;
    }

    protected function getGroups(array $groups = array())
    {
        $ret = array();
        if (!empty($groups)) {
            $ret['group'] = implode(',', $groups);
        }
        return $ret;
    }

    protected function getSqlAndConditions4Count(array $query)
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "SELECT COUNT(*) as num FROM {$className} WHERE {$conditions['conditions']}";
        if (!empty($conditions['for_update'])) {
            $phql = $phql . "  FOR UPDATE ";
            unset($conditions['for_update']);
        }
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4FindOne(array $query)
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "SELECT * FROM {$className} WHERE {$conditions['conditions']} limit 1 ";
        if (!empty($conditions['for_update'])) {
            $phql = $phql . "  FOR UPDATE ";
            unset($conditions['for_update']);
        }
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    /**
     * 查询某个表中的数据
     *
     * @param array $query            
     * @param array $sort            
     * @param int $skip            
     * @param int $limit            
     * @param array $fields            
     */
    protected function getSqlAndConditions4Find(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array())
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $order = $this->getSort($sort);
        $conditions = array_merge($conditions, $order, array(
            'limit' => $limit
        ), array(
            'offset' => $skip
        ));
        $orderBy = "";
        if (!empty($order['order'])) {
            $orderBy = "ORDER BY {$order['order']}";
        }

        $fieldsSql = $this->getFieldsSql($fields);

        $phql = "SELECT {$fieldsSql} FROM {$className} WHERE {$conditions['conditions']} {$orderBy} LIMIT {$conditions['limit']} OFFSET {$conditions['offset']} ";
        if (!empty($conditions['for_update'])) {
            $phql = $phql . "  FOR UPDATE ";
            unset($conditions['for_update']);
        }
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4FindAll(array $query, array $sort = null, array $fields = array())
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $order = $this->getSort($sort);
        $conditions = array_merge($conditions, $order);
        $orderBy = "";
        if (!empty($order['order'])) {
            $orderBy = "ORDER BY {$order['order']}";
        }
        $fieldsSql = $this->getFieldsSql($fields);

        $phql = "SELECT {$fieldsSql} FROM {$className} WHERE {$conditions['conditions']} {$orderBy} ";
        if (!empty($conditions['for_update'])) {
            $phql = $phql . "  FOR UPDATE ";
            unset($conditions['for_update']);
        }
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4Sum(array $query, array $fields = array(), array $groups = array())
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $columns = $this->getColumns($fields);
        $groups = $this->getGroups($groups);
        $params = array_merge($columns, $conditions, $groups);

        $groupBy = "";
        $groupFields = "";
        if (!empty($groups) && !empty($groups['group'])) {
            $groupBy = "GROUP BY {$groups['group']}";
            $groupFields = "{$groups['group']},";
        }

        $summaryFields = '';
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $summaryFields .= "SUM({$field}) AS {$field},";
            }
            $summaryFields = trim($summaryFields, ',');
        }

        $phql = "select {$groupFields} {$summaryFields} FROM {$className} WHERE {$conditions['conditions']} {$groupBy}";
        if (!empty($conditions['for_update'])) {
            $phql = $phql . "  FOR UPDATE ";
            unset($conditions['for_update']);
        }
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4Distinct($field, array $query)
    {
        if (empty($field)) {
            throw new \Exception('请指定字段$field', -999);
        }
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "select DISTINCT {$field} FROM {$className} WHERE {$conditions['conditions']}";
        if (!empty($conditions['for_update'])) {
            $phql = $phql . "  FOR UPDATE ";
            unset($conditions['for_update']);
        }
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    protected function getSqlAndConditions4Insert(array $datas)
    {
        $className = $this->getSource();
        $insertFieldValues = $this->getInsertContents($datas);
        $phql = "INSERT INTO {$className}({$insertFieldValues['fields']}) VALUES ({$insertFieldValues['bindFields']})";
        return array(
            'sql' => $phql,
            'insertFieldValues' => $insertFieldValues
        );
    }

    protected function getSqlAndConditions4Update(array $criteria, array $object, array $options = array())
    {
        if (empty($criteria)) {
            throw new \Exception("更新数据的时候请指定条件", -999);
        }

        $className = $this->getSource();
        $conditions = $this->getConditions($criteria);
        $updateFieldValues = $this->getUpdateContents($object);
        $phql = "UPDATE {$className} SET {$updateFieldValues['fields']} WHERE {$conditions['conditions']} ";
        return array(
            'sql' => $phql,
            'updateFieldValues' => $updateFieldValues,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4Remove(array $query, $isPhysicalRemove = true)
    {
        if (empty($query)) {
            // throw new \Exception("删除数据的时候请指定条件", - 999);
            $query = array();
        }

        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $updateFieldValues = array();
        // 物理删除的话
        if ($isPhysicalRemove) {
            $phql = "DELETE FROM {$className} WHERE {$conditions['conditions']}";
        } else {
            // 如果在后台进行操作数据表的话 
            $__REMOVE_TIME__ = \App\Common\Utils\Helper::getCurrentTime();
            $__REMOVE_USER_ID__ = (isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '');
            $__REMOVE_USER_NAME__ = (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : '');
            $object = array('$set' => array(
                '__REMOVED__' => 1,
                '__REMOVE_TIME__' => $__REMOVE_TIME__,
                '__REMOVE_USER_ID__' => $__REMOVE_USER_ID__,
                '__REMOVE_USER_NAME__' => $__REMOVE_USER_NAME__,
            ));
            $updateFieldValues = $this->getUpdateContents($object);
            $phql = "UPDATE {$className} SET {$updateFieldValues['fields']} WHERE {$conditions['conditions']} ";
        }
        return array(
            'sql' => $phql,
            'updateFieldValues' => $updateFieldValues,
            'conditions' => $conditions
        );
    }

    protected function getFieldsSql($fields)
    {
        $fieldsSql = array();
        if (!empty($fields)) {
            foreach ($fields as $field => $selected) {
                if ($selected) {
                    $fieldsSql[$field] = $field;
                }
            }
        }
        if (empty($fieldsSql)) {
            $fieldsSql = '*';
        } else {
            // 无条件的加上_id字段
            unset($fieldsSql['_id']);
            $fieldsSql = '_id,' . implode(',', array_keys($fieldsSql));
        }
        return $fieldsSql;
    }
}
