<?php

namespace App\Backend\Submodules\Weixin2\Models\Authorize;

class Authorizer extends \App\Common\Models\Weixin2\Authorize\Authorizer
{

    use \App\Backend\Models\Base;

    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('appid', 'asc')
            ->select('appid', 'nick_name')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->appid] = $item->nick_name;
        }
        return $options;
    }
}
