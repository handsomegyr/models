<?php

namespace App\Common\Models\Base;

class Base implements IBase
{

    const MYSQL = 1;

    const MONGODB = 2;

    protected function getClassNameByDb($db, $className)
    {
        $str = "Mysql";
        if ($db == self::MONGODB) {
            $str = "Mongodb";
        }
        $className = sprintf($className, $str);
        // die($className . $db);
        return $className;
    }

    protected $isDebug = false;

    protected $isPhql = false;

    protected $name = null;

    protected $dbName = 'db';

    protected $secondary = false;

    use BaseTrait;

    public static function getEntityModel($source, $dbName = 'db')
    {
        $model = new \App\Common\Models\Base\Mysql\Base();
        // $model->setSource($source);
        // $model->setDebug(true);
        $objEntity = new \App\Common\Models\Base\Base();
        $objEntity->setModel($model);
        $objEntity->setSource($source);
        $objEntity->setDb($dbName);
        // $objEntity->setDebug(true);
        return $objEntity;
    }

    /**
     * model
     *
     * @var \App\Common\Models\Base\IBase
     */
    private $_model = null;

    public function setModel(\App\Common\Models\Base\IBase $model)
    {
        if (empty($model)) {
            throw new \Exception('Model设置错误');
        }
        $this->_model = $model;
        $this->setPhql($this->isPhql);
        $this->setDebug($this->isDebug);
        $this->setDb($this->dbName);
        $this->setSource($this->name);
    }

    public function getModel()
    {
        if (empty($this->_model)) {
            throw new \Exception('Model没有设置');
        }
        return $this->_model;
    }

    /**
     * 设置是否phql
     *
     * @param boolean $isPhql            
     */
    public function setPhql($isPhql)
    {
        return $this->getModel()->setPhql($isPhql);
    }

    public function getPhql()
    {
        return $this->getModel()->getPhql();
    }

    /**
     * 设置是否测试
     *
     * @param boolean $isDebug            
     */
    public function setDebug($isDebug)
    {
        return $this->getModel()->setDebug($isDebug);
    }

    public function getDebug()
    {
        return $this->getModel()->getDebug();
    }

    /**
     * 设置数据源表
     *
     * @param string $source            
     */
    public function setSource($source)
    {
        return $this->getModel()->setSource($source);
    }

    /**
     * 获取数据源表
     */
    public function getSource()
    {
        return $this->getModel()->getSource();
    }

    /**
     * 设置数据源库
     *
     * @param string $dbName            
     */
    public function setDb($dbName)
    {
        return $this->getModel()->setDb($dbName);
    }

    /**
     * 获取数据源库
     */
    public function getDb()
    {
        return $this->getModel()->getDb();
    }

    public function setSecondary($secondary)
    {
        return $this->getModel()->setSecondary($secondary);
    }

    public function getSecondary()
    {
        return $this->getModel()->getSecondary();
    }

    public function begin()
    {
        return $this->getModel()->begin();
    }

    public function commit()
    {
        return $this->getModel()->commit();
    }

    public function rollback()
    {
        return $this->getModel()->rollback();
    }

    public function getDI()
    {
        return $this->getModel()->getDI();
    }

    public function count(array $query)
    {
        return $this->getModel()->count($query);
    }

    public function findOne(array $query)
    {
        return $this->getModel()->findOne($query);
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
        return $this->getModel()->find($query, $sort, $skip, $limit, $fields);
    }

    public function findAll(array $query, array $sort = array(), array $fields = array())
    {
        return $this->getModel()->findAll($query, $sort, $fields);
    }

    public function findAllByCursor(array $query, array $sort = array(), array $fields = array(), callable $callback = null)
    {
        return $this->getModel()->findAllByCursor($query, $sort, $fields, $callback);
    }

    public function distinct($field, array $query)
    {
        return $this->getModel()->distinct($field, $query);
    }

    /**
     * 查询某个表合计信息的数据
     *
     * @param array $query            
     * @param array $fields            
     * @param array $groups            
     */
    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        return $this->getModel()->sum($query, $fields, $groups);
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        return $this->getModel()->insert($datas);
    }

    /**
     * 执行save操作
     *
     * @param array $datas            
     */
    public function save(array $datas)
    {
        return $this->getModel()->save($datas);
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        return $this->getModel()->update($criteria, $object, $options);
    }

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        return $this->getModel()->findAndModify($options);
    }

    public function remove(array $query)
    {
        return $this->getModel()->remove($query);
    }

    public function physicalRemove(array $query)
    {
        return $this->getModel()->physicalRemove($query);
    }

    public function truncate()
    {
        return $this->getModel()->truncate();
    }

    public function selectRaw($sql, array $data = array())
    {
        return $this->getModel()->selectRaw($sql, $data);
    }

    public function selectRawByCursor($sql, array $data = array(), callable $callback = null)
    {
        return $this->getModel()->selectRawByCursor($sql, $data, $callback);
    }

    public function getUploadPath()
    {
        return '';
    }

    public function getPhysicalFilePath($fileName)
    {
        $uploadPath = $this->getUploadPath();
        if (empty($uploadPath)) {
            $filePath = APP_PATH . "public/upload/{$fileName}";
        } else {
            $filePath = APP_PATH . "public/upload/{$uploadPath}/{$fileName}";
        }
        return $filePath;
    }

    public function getImagePath($baseUrl, $image, $x = 0, $y = 0)
    {
        $uploadPath = $this->getUploadPath();
        // return "{$baseUrl}upload/{$uploadPath}/{$image}";
        $xyStr = "";
        if (!empty($x)) {
            $xyStr .= "&w={$x}";
        }
        if (!empty($y)) {
            $xyStr .= "&h={$y}";
        }
        return "{$baseUrl}service/file/index?id={$image}&upload_path={$uploadPath}{$xyStr}";
    }
}
