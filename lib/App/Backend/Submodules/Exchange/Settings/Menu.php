<?php

namespace App\Backend\Submodules\Exchange\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 兑换管理 父节点
        $item = array(
            'menu_name' => '兑换管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 兑换规则
        $item = array(
            'menu_name' => '兑换规则',
            'menu_model' => 'exchange-rule',
            'level' => '兑换管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Exchange\Models\Rule'
        );
        $tree[] = $item;

        // 兑换限制
        $item = array(
            'menu_name' => '兑换限制',
            'menu_model' => 'exchange-limit',
            'level' => '兑换管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Exchange\Models\Limit'
        );
        $tree[] = $item;

        // 兑换记录
        $item = array(
            'menu_name' => '兑换记录',
            'menu_model' => 'exchange-exchange',
            'level' => '兑换管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Exchange\Models\Exchange'
        );
        $tree[] = $item;

        // 兑换日志
        $item = array(
            'menu_name' => '兑换日志',
            'menu_model' => 'exchange-record',
            'level' => '兑换管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Exchange\Models\Record'
        );
        $tree[] = $item;

        return $tree;
    }
}
