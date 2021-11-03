<?php

namespace App\Backend\Submodules\Points\Models;

class Category extends \App\Common\Models\Points\Category
{
    use \App\Backend\Models\Base;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'sort' => 1
        );
        return $sort;
    }

    /**
     * 获取所有列表
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

    /**
     * 获取所有积分分类列表
     *
     * @return array
     */
    public function getAllExcludeParent($isAll = false)
    {
        $query = $this->getQuery();
        // $query['parent_code'] = array('$ne' => '');
        $sort = array('code' => 1);
        $list = $this->findAll($query, $sort);

        $options = array();
        foreach ($list as $item) {
            if ($isAll) {
                $options[$item['code']] = $item;
            } else {
                $options[$item['code']] = $item['name'];
            }
        }
        return $options;
    }
}
