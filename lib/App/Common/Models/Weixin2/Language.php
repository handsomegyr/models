<?php

namespace App\Common\Models\Weixin2;

use App\Common\Models\Base\Base;

class Language extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Language());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('id', 'asc')
            ->select('name', 'value')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->value] = "{$item->value}:$item->name";
        }
        return $options;
    }
}
