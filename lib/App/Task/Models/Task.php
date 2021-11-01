<?php

namespace App\Task\Models;

class Task extends \App\Common\Models\Task\Task
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
