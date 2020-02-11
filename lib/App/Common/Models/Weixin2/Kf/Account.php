<?php

namespace App\Common\Models\Weixin2\Kf;

use App\Common\Models\Base\Base;

class Account extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Kf\Account());
    }
    /**
     * 获取所有列表
     *
     * @return array
     */
    public static function getAll()
    {
        $list = self::orderBy('kf_account', 'asc')
            ->select('kf_account')
            ->get();
        $options = array();
        foreach ($list as $item) {
            $options[$item->kf_account] = "{$item->kf_account}";
        }
        return $options;
    }
}
