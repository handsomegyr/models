<?php

namespace App\Backend\Submodules\Tag\Models;

class Category extends \App\Common\Models\Tag\Category
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有分类列表
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->getQuery();
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        $list = array();
        foreach ($ret as $item) {
            $list[$item['code']] = $item['name'];
        }
        return $list;
    }
}
