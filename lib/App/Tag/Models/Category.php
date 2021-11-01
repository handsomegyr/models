<?php

namespace App\Tag\Models;

class Category extends \App\Common\Models\Tag\Category
{

    /**
     * 默认排序方式
     *            
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        $sort['show_order'] = -1;
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
