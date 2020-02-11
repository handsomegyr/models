<?php

namespace App\Backend\Submodules\Weixin2\Models\User;

class Tag extends \App\Common\Models\Weixin2\User\Tag
{

    use \App\Backend\Models\Base;

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
