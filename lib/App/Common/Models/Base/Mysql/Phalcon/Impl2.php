<?php

namespace App\Common\Models\Base\Mysql\Phalcon;

use App\Common\Models\Base\Mysql\Base;

class Impl2 extends Base
{

    /**
     * model
     *
     * @var \App\Common\Models\Base\Mysql\Base
     */
    protected $model = null;

    public function __construct(\App\Common\Models\Base\Mysql\Base $model)
    {
        if (empty($model)) {
            throw new \Exception('Model设置错误2');
        }
        $this->model = $model;
        // $this->setPhql($this->model->getPhql());
        // $this->setDebug($this->model->getDebug());
        // $this->setDb($this->model->getDb());
        // $this->setSource($this->model->getSource());
    }

    /**
     * 获取Model
     *
     * @var \App\Common\Models\Base\Mysql\Base
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getPhql()
    {
        return $this->getModel()->getPhql();
    }

    public function getDebug()
    {
        return $this->getModel()->getDebug();
    }

    public function getDb()
    {
        return $this->getModel()->getDb();
    }

    public function getSource()
    {
        return $this->getPrefix() . $this->getModel()->getSource();
    }

    public function getSecondary()
    {
        return $this->getModel()->getSecondary();
    }

    /**
     * 数据字段整理
     */
    public function reorganize(array $data)
    {
        return $this->model->reorganize($data);
    }

    public function getDI()
    {
        $di = \Phalcon\Di\Di::getDefault();
        return $di;
    }

    public function begin()
    {
        return $this->getDbFromDi()
            ->begin();
    }

    public function commit()
    {
        return $this->getDbFromDi()
            ->commit();
    }

    public function rollback()
    {
        return $this->getDbFromDi()
            ->rollback();
    }

    public function count(array $query)
    {
        $sqlAndConditions = $this->getSqlAndConditions4Count($query);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind']);
        $result = $result->fetch();
        if (!empty($result)) {
            $num = $result['num'];
        } else {
            $num = 0;
        }
        return $num;
    }

    public function findOne(array $query)
    {
        $sqlAndConditions = $this->getSqlAndConditions4FindOne($query);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind']);
        $info = $result->fetch();
        if (!empty($info)) {
            return $this->reorganize($info);
        } else {
            return array();
        }
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
    public function find(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array())
    {
        $total = $this->count($query);
        // 如果没有数据的话不用在进行sql查询了
        if ($total > 0) {
            $sqlAndConditions = $this->getSqlAndConditions4Find($query, $sort, $skip, $limit, $fields);
            $phql = $sqlAndConditions['sql'];
            $conditions = $sqlAndConditions['conditions'];
            $result = $this->executeQuery($phql, $conditions['bind']);
            $ret = $result->fetchAll();
            $list = array();
            if (!empty($ret)) {
                foreach ($ret as $key => $item) {
                    $list[$key] = $this->reorganize($item);
                }
            }
        } else {
            $list = array();
        }

        return array(
            'total' => $total,
            'datas' => $list
        );
    }

    public function findAll(array $query, array $sort = null, array $fields = array())
    {
        $sqlAndConditions = $this->getSqlAndConditions4FindAll($query, $sort, $fields);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        $list = array();
        if (!empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item);
            }
        }
        return $list;
    }

    public function findAllByCursor(array $query, array $sort = null, array $fields = array(), callable $callback = null)
    {
        $sqlAndConditions = $this->getSqlAndConditions4FindAll($query, $sort, $fields);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind']);
        $list = array();
        while ($item1 = $result->fetch()) {
            $list[] = $item = $this->reorganize($item1);
            if (!empty($callback)) {
                $callback($item);
            }
        }
        return $list;
    }

    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        $sqlAndConditions = $this->getSqlAndConditions4Sum($query, $fields, $groups);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        return $ret;
    }

    public function distinct($field, array $query)
    {
        $sqlAndConditions = $this->getSqlAndConditions4Distinct($field, $query);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        $list = array();
        if (!empty($ret)) {
            foreach ($ret as $item) {
                $data = $this->reorganize($item);
                $list[] = $data[$field];
            }
        }
        return $list;
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        $sqlAndConditions = $this->getSqlAndConditions4Insert($datas);
        $phql = $sqlAndConditions['sql'];
        $insertFieldValues = $sqlAndConditions['insertFieldValues'];
        $data = $insertFieldValues['values'];
        $result = $this->executeQuery($phql, $data, 'execute');
        $_id = $insertFieldValues['_id'];
        return $this->findOne(array(
            '_id' => $_id
        ));
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        $sqlAndConditions = $this->getSqlAndConditions4Update($criteria, $object, $options);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $updateFieldValues = $sqlAndConditions['updateFieldValues'];
        $data = array_merge($updateFieldValues['values'], $conditions['bind']);
        $result = $this->executeQuery($phql, $data, 'execute');
        return $result;
    }

    public function remove(array $query)
    {
        $sqlAndConditions = $this->getSqlAndConditions4Remove($query, false);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $updateFieldValues = $sqlAndConditions['updateFieldValues'];
        $data = array_merge($updateFieldValues['values'], $conditions['bind']);
        $result = $this->executeQuery($phql, $data, 'execute');
        return $result;
    }

    public function physicalRemove(array $query)
    {
        $sqlAndConditions = $this->getSqlAndConditions4Remove($query, true);
        $phql = $sqlAndConditions['sql'];
        $conditions = $sqlAndConditions['conditions'];
        $result = $this->executeQuery($phql, $conditions['bind'], 'execute');
        return $result;
    }

    public function truncate()
    {
        $className = $this->getSource();
        $phql = "TRUNCATE {$className}";
        $result = $this->executeQuery($phql, array(), 'execute');
        return $result;
    }

    public function selectRaw($sql, array $data = array())
    {
        $result = $this->executeDBQuery($sql, $data, 'query');
        $ret = $result->fetchAll();
        $list = array();
        if (!empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item);
            }
        }
        return $list;
    }

    public function selectRawByCursor($sql, array $data = array(), callable $callback = null)
    {
        $result = $this->executeDBQuery($sql, $data, 'query');
        $list = array();
        while ($item1 = $result->fetch()) {
            $list[] = $item = $this->reorganize($item1);
            if (!empty($callback)) {
                $callback($item);
            }
        }
        return $list;
    }


    /**
     * 执行save操作
     *
     * @param array $datas            
     */
    public function save(array $datas)
    {
        return $this->insert($datas);
    }

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        $criteria = array();
        if (isset($options['query'])) {
            $criteria = $options['query'];
        }
        if (empty($criteria)) {
            throw new \Exception("query condition is empty in findAndModify", -999);
        }
        $object = array();
        if (isset($options['update'])) {
            $object = $options['update'];
        }
        if (empty($object)) {
            throw new \Exception("update content is empty in findAndModify", -999);
        }

        $new = false;
        if (isset($options['new'])) {
            $new = $options['new'];
        }
        $upsert = false;
        if (isset($options['upsert'])) {
            $upsert = $options['upsert'];
        }

        try {
            $this->begin();
            // 获取单条记录
            $info = $this->findOne($criteria);

            // 如果没有找到的话
            if (empty($info)) {
                // 如果需要插入的话
                if ($upsert) {
                    array_walk_recursive($criteria, function (&$value, $key) use ($criteria) {
                        if (is_array($value)) {
                            unset($criteria[$key]);
                        }
                    });
                    $datas = array();
                    $datas = array_merge($criteria, $object['$set']);
                    $newInfo = $this->insert($datas);
                } else {
                    throw new \Exception("no record match query condition", -999);
                }
            } else {
                // 进行更新操作
                $criteria['_id'] = $info['_id'];
                $this->update($criteria, $object);
                if ($new) {
                    // 获取单条记录
                    $newInfo = $this->findOne(array(
                        '_id' => $info['_id']
                    ));
                }
            }
            $this->commit();
            // 这里要确认一些mongodb的findAndModify操作的返回值

            $rst = array();
            $rst['ok'] = 1;
            if (empty($new)) {
                $rst['value'] = $info;
            } else {
                $rst['value'] = $newInfo;
            }
        } catch (\Exception $e) {
            $this->rollback();
            $rst = array();
            $rst['ok'] = 0;
            $rst['error'] = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return $rst;
    }

    protected function executeQuery($phql, array $data, $method = 'query')
    {
        try {
            if ($this->getDebug()) {
                echo "<pre><br/>";
                echo $phql . "<br/>";
                var_dump($data);
            }
            $phql = preg_replace('/:(.*?):/i', ':$1', $phql);
            $phql = preg_replace('/\[(.*?)\]/i', '`$1`', $phql);
            if ($this->getDebug()) {
                echo "<pre><br/>";
                echo $phql . "<br/>";
                // die('OK');
            }
            return $this->executeDBQuery($phql, $data, $method);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function executeDBQuery($phql, array $data, $method = 'query')
    {
        $db = $this->getDbFromDi();
        // 只有在读取数据的时候，如果设置了secondary的话
        if ($method == 'query' && $this->getSecondary()) {
            $di = $this->getDI();
            $db = $di['secondarydb'];
        }
        $result = $db->$method($phql, $data);
        if ($method == 'query') {
            // die('executeDBQuery:' . MYDB_FETCH_ASSOC);
            $result->setFetchMode(MYDB_FETCH_ASSOC);
            return $result;
        } else {
            return $db->affectedRows();
        }
    }

    protected function getDbFromDi()
    {
        $dbname = $this->getDb();
        if (empty($dbname)) {
            $dbname = 'db';
        }
        $di = $this->getDI();
        $db = $di[$dbname];
        return $db;
    }
}
