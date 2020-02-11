<?php

namespace App\Common\Models\Weixin2\User;

use App\Common\Models\Base\Base;

class Tag extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\User\Tag());
    }

    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAllByType($field = "id")
    {
        $list = self::where("tag_id", ">", 0)->orderBy('id', 'asc')
            ->select('name', 'id', 'tag_id')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->$field] = $item->name;
        }
        return $options;
    }
}
