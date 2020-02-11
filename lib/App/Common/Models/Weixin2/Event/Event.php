<?php

namespace App\Common\Models\Weixin2\Event;

use App\Common\Models\Base\Base;

class Event extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Event\Event());
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
