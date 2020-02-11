<?php

namespace App\Backend\Submodules\Weixin2\Models\Kf;

class Account extends \App\Common\Models\Weixin2\Kf\Account
{

    use \App\Backend\Models\Base;
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
