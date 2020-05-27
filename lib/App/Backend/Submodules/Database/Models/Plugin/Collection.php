<?php

namespace App\Backend\Submodules\Database\Models\Plugin;

class Collection extends \App\Common\Models\Database\Plugin\Collection
{
    use \App\Backend\Models\Base;
    
    /**
     * 获取所有数据库表列表
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
            $list[$item['_id']] = $item['name'];
        }
        return $list;
    }
}
