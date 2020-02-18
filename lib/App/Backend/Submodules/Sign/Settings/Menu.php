<?php

namespace App\Backend\Submodules\Sign\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 签到管理 父节点
        $item = array(
            'menu_name' => '签到管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 用户签到
        $item = array(
            'menu_name' => '用户签到',
            'menu_model' => 'sign-sign',
            'level' => '签到管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Sign\Models\Sign'
        );
        $tree[] = $item;

        // 签到日志
        $item = array(
            'menu_name' => '签到日志',
            'menu_model' => 'sign-log',
            'level' => '签到管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Sign\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
