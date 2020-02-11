<?php

namespace App\Common\Models\Weixin2\Material;

use App\Common\Models\Base\Base;

class Material extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Material\Material());
    }
    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public static function getAllByType($type, $field = "id")
    {
        if (!empty($type)) {
            $list = self::where("type", $type)->orderBy('id', 'asc')
                ->select('name', 'id', 'media_id')
                ->get();
        } else {
            $list = self::orderBy('id', 'asc')
                ->select('name', 'id', 'media_id')
                ->get();
        }
        $options = array();
        foreach ($list as $item) {
            $options[$item->$field] = $item->name;
        }
        return $options;
    }
}
