<?php

namespace App\Backend\Models;

class Input extends \stdClass
{
    // 为字符串字段进行设置检索条件 like, eq, prelike, 默认是eq
    private $sqlWhere4String = 'eq';
    public function setSqlWhere4String($sqlwhere)
    {
        $this->sqlWhere4String = strtolower(trim($sqlwhere));
    }

    protected $filter = NULL;

    protected $schemas = array();

    /**
     * 过滤信息
     */
    public function getFilter()
    {
        if (empty($this->filter)) {
            $schemas = $this->getSchemas();
            $this->filter = array();
            if (!empty($schemas)) {
                foreach ($schemas as $key => $field) {
                    $this->filter[$key] = urldecode(trim($this->$key));
                }
            }

            $this->filter['sort_by'] = trim($this->sort_by);
            $this->filter['sort_order'] = trim($this->sort_order);
            /* 分页大小 */
            // 每页显示数量
            if (isset($this->page_size) && intval($this->page_size) > 0) {
                $this->filter['page_size'] = intval($this->page_size);
            } else {
                $this->filter['page_size'] = 10;
            }

            // 当前页数
            $this->filter['page'] = (empty($this->page) || intval($this->page) <= 0) ? 1 : intval($this->page);

            // offset
            $this->filter['start'] = ($this->filter['page'] - 1) * $this->filter['page_size'];
        }
        return $this->filter;
    }

    public function clearFilter()
    {
        $this->filter = NULL;
    }

    public function setRecordCount($record_count)
    {
        /* page 总数 */
        $this->filter['record_count'] = $record_count;
        $this->filter['page_count'] = (!empty($this->filter['record_count'])) ? ceil($this->filter['record_count'] / $this->filter['page_size']) : 1;

        /* 边界处理 */
        if ($this->filter['page'] > $this->filter['page_count']) {
            $this->filter['page'] = $this->filter['page_count'];
        }
        // setcookie('backend[lastfilter]', urlencode(serialize($this->filter)), time() + 600);
    }

    public function getSchemas()
    {
        return $this->schemas;
    }

    public function setSchemas($schemas)
    {
        if (empty($schemas)) {
            throw new \Exception('schemas is empty');
        }
        $this->clearFilter();
        return $this->schemas = $schemas;
    }

    public function addSchema($key, array $field)
    {
        $this->schemas[$key] = $field;
        $this->clearFilter();
    }

    function __call($method, $args)
    {
        $this->clearFilter();
        if (isset($this->$method) && is_callable($this->$method))
            return call_user_func_array($this->$method, $args);
        else
            throw new \Exception("{$method} is not set or callable", -1);
    }

    protected $defaultQuery = array();
    public function setDefaultQuery($defaultQuery)
    {
        if (empty($defaultQuery)) {
            $defaultQuery = array();
        }
        $this->defaultQuery = $defaultQuery;
        $this->clearFilter();
    }

    /**
     * 根据画面条件获取查询条件
     *
     * @return array
     */
    public function getQuery()
    {
        $filter = $this->getFilter();
        $schemas = $this->getSchemas();

        $where = array();
        if (!empty($schemas)) {
            foreach ($schemas as $key => $field) {
                if (isset($filter[$key])) {
                    if (strlen($filter[$key]) > 0) {
                        if ($field['data']['type'] == "datetime") {
                            $datetime = urldecode($filter[$key]);
                            $isExist = $this->isCheckPeriodFlagExist($datetime);
                            if ($isExist) {
                                $datatimeArr = explode('|', $datetime);
                                if (!empty($datatimeArr[0])) {
                                    $where[$key]['$gte'] = \App\Common\Utils\Helper::getCurrentTime(strtotime($datatimeArr[0]));
                                }
                                if (!empty($datatimeArr[1])) {
                                    $where[$key]['$lte'] = \App\Common\Utils\Helper::getCurrentTime(strtotime($datatimeArr[1]));
                                }
                            } else {
                                $where[$key] = \App\Common\Utils\Helper::getCurrentTime(strtotime($datetime));
                            }
                        } elseif ($field['data']['type'] == "integer") {
                            $num = urldecode($filter[$key]);
                            $isExist = $this->isCheckPeriodFlagExist($num);
                            if ($isExist) {
                                $numArr4Explode = explode('|', $num);
                                $numArr = array();
                                // 对应0|0的情况
                                if (isset($numArr4Explode[0]) && strlen($numArr4Explode[0]) > 0) {
                                    $numArr[0] = ($numArr4Explode[0]);
                                }
                                if (isset($numArr4Explode[1]) && strlen($numArr4Explode[1]) > 0) {
                                    $numArr[1] = ($numArr4Explode[1]);
                                }
                                if (isset($numArr[0])) {
                                    $where[$key]['$gte'] = $numArr[0];
                                }
                                if (isset($numArr[1])) {
                                    $where[$key]['$lte'] = $numArr[1];
                                }
                            } else {
                                $where[$key] = $num;
                            }
                        } elseif ($field['data']['type'] == "boolean") {
                            $where[$key] = intval(urldecode($filter[$key]));
                        } else {
                            $str1 = urldecode($filter[$key]);
                            $isExist = $this->isCheckPeriodFlagExist($str1);
                            if ($isExist) {
                                $strArr = explode('|', $str1);
                                if (!empty($strArr[0])) {
                                    $where[$key]['$gte'] = $strArr[0];
                                }
                                if (!empty($strArr[1])) {
                                    $where[$key]['$lte'] = $strArr[1];
                                }
                            } else {
                                if ((!empty($field['search']) && !empty($field['search']['sqlWhere']))) {
                                    if (strtolower(trim($field['search']['sqlWhere'])) == 'eq') {
                                        $where[$key] = $str1;
                                    } elseif (strtolower(trim($field['search']['sqlWhere'])) == 'prelike') {
                                        $where[$key]['$like'] = $str1 . '%';
                                    } else {
                                        $where[$key]['$like'] = '%' . $str1 . '%';
                                    }
                                } else {
                                    if ($this->sqlWhere4String == 'eq') {
                                        $where[$key] = $str1;
                                    } elseif ($this->sqlWhere4String == 'prelike') {
                                        $where[$key]['$like'] = $str1 . '%';
                                    } else {
                                        $where[$key]['$like'] = '%' . $str1 . '%';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // 如果有默认条件的话进行合并
        if (!empty($this->defaultQuery)) {
            $where = array_merge($where, $this->defaultQuery);
        }
        return $where;
    }

    public function getSort()
    {
        $filter = $this->getFilter();
        // 排序方式
        $sort = array();
        $sort[$filter['sort_by']] = ('desc' == strtolower($filter['sort_order'])) ? -1 : 1;
        return $sort;
    }

    public function getOffset()
    {
        $filter = $this->getFilter();
        return $filter['start'];
    }

    public function getLimit()
    {
        $filter = $this->getFilter();
        return $filter['page_size'];
    }

    public function getFormData($is_update = true)
    {
        $schemas = $this->getSchemas();
        $data = array();
        if (!empty($schemas)) {
            foreach ($schemas as $key => $field) {
                if ($field['data']['type'] == "string") {

                    if (isset($this->$key) && $is_update) {
                        $data[$key] = urldecode(trim($this->$key));
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : "";
                    }
                } elseif ($field['data']['type'] == "integer") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = intval($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : 0;
                    }
                } elseif ($field['data']['type'] == "float") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = floatval($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : 0.0;
                    }
                } elseif ($field['data']['type'] == "decimal") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = doubleval($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : 0.00;
                    }
                } elseif ($field['data']['type'] == "datetime") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = \App\Common\Utils\Helper::getCurrentTime(strtotime($this->$key));
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : \App\Common\Utils\Helper::getCurrentTime();
                    }
                } elseif ($field['data']['type'] == "boolean") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = empty($this->$key) ? false : true;
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : false;
                    }
                } elseif ($field['data']['type'] == "json") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = json_decode(urldecode(trim($this->$key)), true);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : array();
                    }
                } elseif ($field['data']['type'] == "array") {
                    if (isset($this->$key) && $is_update) {
                        if (is_array($this->$key)) {
                            $data[$key] = $this->$key;
                        } else {
                            throw new \ErrorException("{$key} is not array", -99);
                        }
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : array();
                    }
                } elseif ($field['data']['type'] == "html") {
                    if (isset($this->$key) && $is_update) {
                        $data[$key] = trim($this->$key);
                    } else {
                        $data[$key] = isset($field['data']['defaultValue']) ? $field['data']['defaultValue'] : '';
                    }
                } elseif ($field['data']['type'] == "file") {
                    unset($data[$key]);
                    if (!$is_update) {
                        $data[$key] = '';
                    } else {
                        if (isset($this->$key)) {
                            $data[$key] = trim($this->$key);
                        }
                    }
                } elseif ($field['data']['type'] == "multifile") {
                    unset($data[$key]);
                    if (!$is_update) {
                        $data[$key] = '';
                    } else {
                        if (isset($this->$key)) {
                            $data[$key] = ($this->$key);
                        }
                    }
                } else {
                    $data[$key] = "";
                }
            }
        }

        if ($is_update) {
            unset($data['_id']);
        } else {
            $data['_id'] = "";
        }
        return $data;
    }

    protected function isCheckPeriodFlagExist($mystring)
    {
        $findme   = '|';
        $pos = strpos($mystring, $findme);

        // 使用 !== 操作符。使用 != 不能像我们期待的那样工作，
        // 因为 'a' 的位置是 0。语句 (0 != false) 的结果是 false。
        if ($pos !== false) {
            return true;
        } else {
            return false;
        }
    }
}
