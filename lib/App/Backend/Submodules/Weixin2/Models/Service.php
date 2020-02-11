<?php

namespace App\Backend\Submodules\Weixin2\Models;

class Service extends \App\Common\Models\Weixin2\Service
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('id', 'asc')
            ->select('name', 'id')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->id] = $item->name;
        }
        return $options;
    }
}
