<?php

namespace App\Backend\Submodules\Database\Models;

class Plugin extends \App\Common\Models\Database\Plugin
{
    use \App\Backend\Models\Base;

    /**
     * 获取所有插件列表
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
