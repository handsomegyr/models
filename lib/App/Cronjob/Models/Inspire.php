<?php

namespace App\Cronjob\Models;

class Inspire extends \App\Common\Models\Cronjob\Inspire
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
