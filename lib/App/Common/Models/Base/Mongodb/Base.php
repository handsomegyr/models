<?php

namespace App\Common\Models\Base\Mongodb;

class Base implements \App\Common\Models\Base\IBase
{

    const DB_ADMIN = 'admin';

    const DB_BACKUP = 'backup';

    const DB_MAPREDUCE = 'mapreduce';

    const DB_LOGS = 'logs';

    const GRIDFS_PREFIX = 'grid_fs';

    const DEFAULT_DATABASE = 'default';

    const DEFAULT_CLUSTER = 'cluster1';

    protected $isDebug = false;

    protected $isPhql = false;

    protected $name = null;

    protected $dbName = 'db';

    protected $secondary = false;

    use BaseTrait;

    private $impl = NULL;

    public function __construct()
    {
        $this->impl = new \App\Common\Models\Base\Mongodb\Default1\Impl($this);
    }

    public function setPhql($isPhql)
    {
        $this->isPhql = $isPhql;
    }

    public function getPhql()
    {
        return $this->isPhql;
    }

    public function setDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    public function getDebug()
    {
        return $this->isDebug;
    }

    public function setSource($source)
    {
        $this->name = $source;
    }

    public function getSource()
    {
        return $this->name;
    }

    public function setDb($db)
    {
        $this->dbName = $db;
    }

    public function getDb()
    {
        return $this->dbName;
    }

    public function setSecondary($secondary)
    {
        $this->secondary = $secondary;
    }

    public function getSecondary()
    {
        return $this->secondary;
    }

    public function reorganize(array $data)
    {
        if (isset($data['_id'])) {
            $data['_id'] = $this->getMongoId4Query($data['_id']);
        }
        if (isset($data['__CREATE_TIME__'])) {
            $data['__CREATE_TIME__'] = $this->changeToValidDate($data['__CREATE_TIME__']);
        }
        if (isset($data['__MODIFY_TIME__'])) {
            $data['__MODIFY_TIME__'] = $this->changeToValidDate($data['__MODIFY_TIME__']);
        }
        if (isset($data['__REMOVED__'])) {
            $data['__REMOVED__'] = $this->changeToBoolean($data['__REMOVED__']);
        }
        if (isset($data['memo'])) {
            $data['memo'] = $this->changeToArray($data['memo']);
        }
        return $data;
    }

    public function getDI()
    {
        return $this->impl->getDI();
    }

    public function begin()
    {
        return $this->impl->begin();
    }

    public function commit()
    {
        return $this->impl->commit();
    }

    public function rollback()
    {
        return $this->impl->rollback();
    }

    public function count(array $query)
    {
        return $this->impl->count($query);
    }

    public function findOne(array $query)
    {
        return $this->impl->findOne($query);
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
        return $this->impl->find($query, $sort, $skip, $limit, $fields);
    }

    public function findAll(array $query, array $sort = null, array $fields = array())
    {
        return $this->impl->findAll($query, $sort, $fields);
    }

    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        return $this->impl->sum($query, $fields, $groups);
    }

    public function distinct($field, array $query)
    {
        return $this->impl->distinct($field, $query);
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        return $this->impl->insert($datas);
    }

    /**
     * 执行save操作
     *
     * @param array $datas            
     */
    public function save(array $datas)
    {
        return $this->impl->save($datas);
    }

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        return $this->impl->findAndModify($options);
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        return $this->impl->update($criteria, $object, $options);
    }

    public function remove(array $query)
    {
        return $this->impl->remove($query);
    }

    public function physicalRemove(array $query)
    {
        return $this->impl->remove($query);
    }

    public function truncate()
    {
        return $this->impl->truncate();
    }
}
