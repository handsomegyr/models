<?php

namespace App\Backend\Submodules\Weixin2\Models\Component;

class Component extends \App\Common\Models\Weixin2\Component\Component
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('appid', 'asc')
            ->select('appid', 'name')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->appid] = $item->name;
        }
        return $options;
    }
}
