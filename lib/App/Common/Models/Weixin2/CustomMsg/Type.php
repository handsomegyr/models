<?php

namespace App\Common\Models\Weixin2\CustomMsg;

use App\Common\Models\Base\Base;

class Type extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\CustomMsg\Type());
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
            $options[$item->value] = $item->name;
        }
        return $options;
    }
}
