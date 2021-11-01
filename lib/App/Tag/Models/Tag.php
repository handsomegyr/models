<?php

namespace App\Tag\Models;

class Tag extends \App\Common\Models\Tag\Tag
{

    /**
     * 默认排序方式
     *            
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        $sort['id'] = -1;
        return $sort;
    }

    /**
     * 默认查询条件
     *
     * @return array
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }
}
