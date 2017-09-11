<?php
namespace App\Sign\Models;
class Sign extends \App\Common\Models\Sign\Sign
{
    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => -1
        );
        return $sort;
    }
    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }
}