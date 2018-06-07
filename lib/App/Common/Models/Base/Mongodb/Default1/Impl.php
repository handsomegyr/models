<?php
namespace App\Common\Models\Base\Mongodb\Default1;

use App\Common\Models\Base\Mongodb\Base;

class Impl extends Base
{

    protected $model = NULL;

    public function __construct($model)
    {
        $this->model = $model;
        
        $this->setPhql($this->model->getPhql());
        $this->setDebug($this->model->getDebug());
        $this->setDb($this->model->getDb());
        $this->setSource($this->model->getSource());
        
        $this->_model = new MongoCollectionAdapter($this->model->getSource());
    }

    private $_model;

    public function setDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * 数据字段整理
     */
    public function reorganize(array $data)
    {
        return $this->model->reorganize($data);
    }

    public function getSource()
    {
        return $this->model->getSource();
    }

    public function getDI()
    {
        $di = \Phalcon\DI::getDefault();
        return $di;
    }

    public function begin()
    {
        return true;
    }

    public function commit()
    {
        return true;
    }

    public function rollback()
    {
        return false;
    }

    public function count(array $query)
    {
        $query = $this->toArray($query);
        $ret = $this->_model->count($query);
        return $this->result($ret);
    }

    public function findOne(array $query)
    {
        $query = $this->toArray($query);
        if (! empty($fields)) {
            $fields = $this->toArray($fields);
        } else {
            $fields = array();
        }
        $rst = $this->_model->findOne($query, $fields);
        $info = $this->result($rst);
        if (! empty($info)) {
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
        $query = $this->toArray($query);
        $sort = $this->toArray($sort);
        $skip = intval($skip);
        $skip = $skip > 0 ? $skip : 0;
        $limit = intval($limit);
        $limit = $limit < 0 ? 10 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;
        if (! empty($fields)) {
            $fields = $this->toArray($fields);
        } else {
            $fields = array();
        }
        
        $cursor = $this->_model->find($query, $fields);
        $total = $cursor->count();
        if (! empty($sort))
            $cursor->sort($sort);
        if ($skip > 0)
            $cursor->skip($skip);
        $cursor->limit($limit);
        
        $rst = array(
            'datas' => iterator_to_array($cursor, false),
            'total' => $total
        );
        $ret = $this->result($rst);
        
        $list = array();
        if (! empty($ret['datas'])) {
            foreach ($ret['datas'] as $key => $item) {
                $list[$key] = $this->reorganize($item);
            }
        }
        
        return array(
            'total' => $total,
            'datas' => $list
        );
    }

    public function findAll(array $query, array $sort = null, array $fields = array())
    {
        $query = $this->toArray($query);
        $sort = $this->toArray($sort);
        if (empty($sort)) {
            $sort = array(
                '_id' => - 1
            );
        }
        $fields = $this->toArray($fields);
        $rst = $this->_model->findAll($query, $sort, 0, 0, $fields);
        
        $ret = $this->result($rst);
        
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item);
            }
        }
        return $list;
    }

    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        if (empty($fields)) {
            return array();
        }
        
        if (empty($groups)) {
            return array();
        }
        
        $ops = array();
        if (! empty($query)) {
            $ops[] = array(
                '$match' => $query
            );
        }
        
        $groupFieldsSum = array();
        foreach ($fields as $field) {
            $groupFieldsSum[$field] = array(
                '$sum' => '$' . $field
            );
        }
        
        $groupFields = array();
        foreach ($groups as $group) {
            $groupFields[$group] = '$' . $group;
        }
        
        $groupOps = array(
            '_id' => $groupFields
        );
        foreach ($groupFieldsSum as $key => $fieldsSum) {
            $groupOps[$key] = $fieldsSum;
        }
        
        $ops[] = array(
            '$group' => $groupOps
        );
        // echo "<pre>";
        // print_r($ops);
        // die('xx');
        $rst = $this->aggregate($ops, null, null);
        // Array ( [ok] => 1 [result] => Array ( [0] => Array ( [_id] => Array ( [_id] => MongoId Object ( [objectID:MongoId:private] => MongoDB\BSON\ObjectId Object ( [oid] => 5b17cef969dc0a08131fce43 ) ) ) [sysMsgCount] => 1 ) ) [waitedMS] => 0 )
        $this->doError($rst);
        
        $list = array();
        if (! empty($rst['result'])) {
            foreach ($rst['result'] as $item) {
                $item1 = array();
                foreach ($groups as $group) {
                    $item1[$group] = $item['_id'][$group];
                }
                foreach (array_keys($groupFieldsSum) as $fieldsSum) {
                    $item1[$fieldsSum] = $item[$fieldsSum];
                }
                $item1 = $this->reorganize($item1);
                $list[] = $item1;
            }
        }
        return $list;
    }

    public function distinct($field, array $query)
    {
        $key = is_string($field) ? trim($field) : '';
        $query = $this->toArray($query);
        $rst = $this->_model->distinct($key, $query);
        $ret = $this->result($rst);
        return $ret;
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        $datas = $this->toArray($datas);
        $rst = $this->_model->insertByFindAndModify($datas);
        $info = $this->result($rst);
        if (! empty($info)) {
            return $this->reorganize($info);
        } else {
            return array();
        }
    }

    /**
     * 保存数据，$datas中如果包含_id属性，那么将更新_id的数据，否则创建新的数据
     *
     * @param string $datas            
     * @throws \Exception
     * @return string
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
        $options = $this->toArray($options);
        $rst = $this->_model->findAndModifyByCommand($options);
        // ERROR的情况1 Array ( [lastErrorObject] => Array ( [updatedExisting] => [n] => 0 ) [value] => [ok] => 1 ) xxx
        // OK的情况 Array ( [lastErrorObject] => Array ( [updatedExisting] => 1 [n] => 1 ) [value] => Array ( [_id] => MongoId Object ( [objectID:MongoId:private] => MongoDB\BSON\ObjectId Object ( [oid] => 5b18941869dc0a07c253ccd2 ) ) [user_id] => guoyongrong5b189418943a3 [sysMsgCount] => 2 [privMsgCount] => 5 [friendMsgCount] => 2 [replyMsgCount] => 1 [__CREATE_TIME__] => MongoDate Object ( [sec] => 1528337432 [usec] => 607000 ) [__MODIFY_TIME__] => MongoDate Object ( [sec] => 1528337432 [usec] => 610000 ) [__REMOVED__] => ) [ok] => 1 )
        $this->doError($rst);
        $rst = $this->result($rst);
        // 增加reorganize处理
        if (! empty($rst['value'])) {
            $rst['value'] = $this->reorganize($rst['value']);
        }
        return $rst;
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        $criteria = $this->toArray($criteria);
        $object = $this->toArray($object);
        $options = $this->toArray($options);
        $rst = $this->_model->update($criteria, $object, $options);
        // Array ( [ok] => 1 [nModified] => 1 [n] => 1 [err] => [errmsg] => [updatedExisting] => 1 )
        $this->doError($rst);
        return $this->result($rst);
    }

    public function remove(array $query)
    {
        $criteria = $this->toArray($query);
        $rst = $this->_model->physicalRemove($criteria);
        // Array ( [ok] => 1 [n] => 1 [err] => [errmsg] => )
        return $this->result($rst);
    }

    /**
     * 规范返回数据的格式为数组
     *
     * @param mixed $rst            
     * @return array
     */
    private function result($rst)
    {
        return $rst;
    }

    /**
     * 将字符串转化为数组
     *
     * @param string $string            
     * @throws \Exception
     * @return array
     */
    private function toArray($rst)
    {
        if (is_array($rst)) {
            array_walk_recursive($rst, function (&$value, $key) {
                if ($key === '_id' && strlen($value) === 24) {
                    if (! ($value instanceof \MongoId))
                        $value = new \MongoId($value);
                }
            });
        }
        return $rst;
    }

    private function doError($rst)
    {
        if (empty($rst['ok'])) {
            // [err] => [errmsg] =>
            $error_msg = '数据库错误发生';
            if (! empty($rst['err'])) {
                $error_msg .= ' err: ' . $rst['err'];
            }
            if (! empty($rst['errmsg'])) {
                $error_msg .= ' errmsg: ' . $rst['errmsg'];
            }
            throw new \Exception($error_msg);
        }
    }

    /**
     * 过载处理
     *
     * @param string $funcname            
     * @param array $arguments            
     * @return mixed
     */
    public function __call($funcname, $arguments)
    {
        if (! is_array($arguments)) {
            $arguments = $newArgument = array();
        } else {
            $newArgument = array();
            foreach ($arguments as $argument) {
                $newArgument[] = $this->toArray($argument);
            }
        }
        return call_user_func_array(array(
            $this->_model,
            $funcname
        ), $newArgument);
    }

    /**
     * 设定数据来自于从库
     *
     * @param string $flag            
     */
    public function setReadFromSecondary($flag = false)
    {
        $this->_model->setReadPreference($flag == true ? 'secondaryPreferred' : 'primaryPreferred');
    }

    /**
     * 批量插入
     *
     * @param string $a            
     * @param array $option            
     * @throws \Exception
     * @return string
     */
    public function batchInsert($a, $option = array('continueOnError'=>true))
    {
        $a = $this->toArray($a);
        $rst = $this->_model->batchInsert($a, $option);
        return $this->result($rst);
    }

    /**
     * 清空整个集合
     *
     * @return string
     */
    public function drop()
    {
        $rst = $this->_model->drop();
        return $this->result($rst);
    }

    /**
     * 创建索引
     *
     * @param string $keys            
     * @param string $options            
     * @return string
     */
    public function ensureIndex($keys, $options)
    {
        if (! empty($keys)) {
            $keys = $this->toArray($keys);
        } else {
            $keys = trim($keys);
        }
        
        if (! empty($options)) {
            $options = $this->toArray($options);
        } else {
            $options = array(
                'background' => true
            );
        }
        $rst = $this->_model->ensureIndex($keys, $options);
        return $this->result($rst);
    }

    /**
     * 删除特定索引
     *
     * @param string $keys            
     * @return string
     */
    public function deleteIndex($keys)
    {
        $keys = $this->toArray($keys);
        $rst = $this->_model->deleteIndex($keys);
        return $this->result($rst);
    }

    /**
     * 删除全部索引
     *
     * @return string
     */
    public function deleteIndexes()
    {
        $rst = $this->_model->deleteIndexes();
        return $this->result($rst);
    }

    /**
     * aggregate框架支持
     *
     * @param string $ops1            
     * @param string $ops2            
     * @param string $ops3            
     * @return string
     */
    public function aggregate($ops1, $ops2, $ops3)
    {
        $param_arr = array();
        $ops1 = $this->toArray($ops1);
        $ops2 = $this->toArray($ops2);
        $ops3 = $this->toArray($ops3);
        
        $param_arr[] = $ops1;
        if (! empty($ops2)) {
            $param_arr[] = $ops2;
        }
        if (! empty($ops3)) {
            $param_arr[] = $ops3;
        }
        
        $rst = call_user_func_array(array(
            $this->_model,
            'aggregate'
        ), $param_arr);
        return $this->result($rst);
    }

    /**
     * 存储小文件文件到集群（2M以内的文件）
     *
     * @param string $fileBytes            
     * @param string $fileName            
     * @return string
     */
    public function uploadFile($fileBytes, $fileName)
    {
        if (base64_encode(base64_decode($fileBytes, true)) === $fileBytes) {
            $fileBytes = base64_decode($fileBytes);
        }
        
        $rst = $this->_model->storeBytesToGridFS($fileBytes, $fileName, array(
            'collection_id' => $this->_collection_id,
            'project_id' => $this->_project_id
        ));
        return $this->result($rst);
    }

    /**
     * 一次请求，执行一些列操作，降低网络传输导致的效率问题
     *
     * @param string $ops            
     * @param bool $last
     *            只返回最后一条处理的结果
     * @return string
     */
    public function pipe($ops, $last = true)
    {
        $rst = array();
        $ops = $this->toArray($ops);
        if (empty($ops)) {
            return $this->result($ops);
        }
        foreach ($ops as $cmd => $param_arr) {
            $execute = call_user_func_array(array(
                $this->_model,
                $cmd
            ), $param_arr);
            
            if ($last)
                $rst = $execute;
            else
                $rst[] = $execute;
        }
        return $this->result($rst);
    }
}
