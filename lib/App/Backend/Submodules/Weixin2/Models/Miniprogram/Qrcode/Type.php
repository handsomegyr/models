<?php

namespace App\Backend\Submodules\Weixin2\Models\Miniprogram\Qrcode;

class Type extends \App\Common\Models\Weixin2\Miniprogram\Qrcode\Type
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
