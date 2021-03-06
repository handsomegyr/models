<?php

namespace App\Common\Models\Base;

interface IBase
{

    /**
     * 设置是否Phql
     *
     * @param boolean $isPhql            
     */
    public function setPhql($isPhql);

    public function getPhql();

    /**
     * 设置是否测试
     *
     * @param boolean $isDebug            
     */
    public function setDebug($isDebug);

    public function getDebug();

    /**
     * 设置数据源表
     *
     * @param string $source            
     */
    public function setSource($source);

    public function getSource();

    /**
     * 设置数据源库
     *
     * @param string $dbName            
     */
    public function setDb($dbName);

    public function getDb();

    /**
     * 设置是否是只读数据源
     *
     * @param boolean $secondary            
     */
    public function setSecondary($secondary);

    public function getSecondary();

    /**
     * 数据库操作
     */
    public function begin();

    public function commit();

    public function rollback();

    public function getDI();

    public function count(array $query);

    public function findOne(array $query);

    /**
     * 查询某个表中的数据
     *
     * @param array $query            
     * @param array $sort            
     * @param int $skip            
     * @param int $limit            
     * @param array $fields            
     */
    public function find(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array());

    public function findAll(array $query, array $sort = array(), array $fields = array());
    public function findAllByCursor(array $query, array $sort = array(), array $fields = array(), callable $callback = null);

    public function distinct($field, array $query);

    /**
     * 查询某个表合计信息的数据
     *
     * @param array $query            
     * @param array $fields            
     * @param array $groups            
     */
    public function sum(array $query, array $fields = array(), array $groups = array());

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas);

    /**
     * 执行save操作
     *
     * @param array $datas            
     */
    public function save(array $datas);

    /**
     * 执行update操作
     *
     * @param array $criteria     
     * @param array $object       
     * @param array $options       
     */
    public function update(array $criteria, array $object, array $options = array());

    /**
     * findAndModify
     */
    public function findAndModify(array $options);

    /**
     * 执行删除操作
     *
     * @param array $query            
     */
    public function remove(array $query);

    /**
     * 执行物理删除操作
     *
     * @param array $query            
     */
    public function physicalRemove(array $query);

    /**
     * 执行TRUNCATE操作            
     */
    public function truncate();

    /**
     * 直接执行sql查询
     */
    public function selectRaw($sql, array $data = array());
    public function selectRawByCursor($sql, array $data = array(), callable $callback = null);
}
