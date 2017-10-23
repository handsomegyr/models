<?php
namespace App\Alipay\Models;

class ScriptTracking extends \App\Common\Models\Alipay\ScriptTracking
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => - 1
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