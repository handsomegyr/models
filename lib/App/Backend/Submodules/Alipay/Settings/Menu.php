<?php

namespace App\Backend\Submodules\Alipay\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 支付宝管理 父节点
        $item = array(
            'menu_name' => '支付宝管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 应用设置
        $item = array(
            'menu_name' => '应用设置',
            'menu_model' => 'alipay-application',
            'level' => '支付宝管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Alipay\Models\Application'
        );
        $tree[] = $item;

        // 回调地址安全域名
        $item = array(
            'menu_name' => '回调地址安全域名',
            'menu_model' => 'alipay-callbackurls',
            'level' => '支付宝管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Alipay\Models\Callbackurls'
        );
        $tree[] = $item;

        // 用户
        $item = array(
            'menu_name' => '用户',
            'menu_model' => 'alipay-user',
            'level' => '支付宝管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Alipay\Models\User'
        );
        $tree[] = $item;

        // 授权执行时间跟踪统计
        $item = array(
            'menu_name' => '授权执行时间跟踪统计',
            'menu_model' => 'alipay-scripttracking',
            'level' => '支付宝管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Alipay\Models\ScriptTracking'
        );
        $tree[] = $item;

        return $tree;
    }
}
