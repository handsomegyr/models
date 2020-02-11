<?php

namespace App\Backend\Submodules\Weixin2\Models\ReplyMsg;

class ReplyMsg extends \App\Common\Models\Weixin2\ReplyMsg\ReplyMsg
{

    use \App\Backend\Models\Base;

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public static function getAllByType($msg_type, $field = "id")
    {
        if (!empty($msg_type)) {
            $list = self::where("msg_type", $msg_type)->orderBy('id', 'asc')
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
