<?php

namespace App\Backend\Submodules\Weixin2\Models\Material;

class Material extends \App\Common\Models\Weixin2\Material\Material
{

    use \App\Backend\Models\Base;
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
