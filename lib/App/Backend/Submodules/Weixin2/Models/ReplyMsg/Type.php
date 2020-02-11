<?php

namespace App\Backend\Submodules\Weixin2\Models\ReplyMsg;

class Type extends \App\Common\Models\Weixin2\ReplyMsg\Type
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
            ->select('name', 'value')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->value] = $item->name;
        }
        return $options;
    }
}
