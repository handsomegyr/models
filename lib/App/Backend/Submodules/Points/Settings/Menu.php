<?php

namespace App\Backend\Submodules\Points\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 积分管理 父节点
        $item = array(
            'menu_name' => '积分管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 积分分类
        $item = array(
            'menu_name' => '积分分类',
            'menu_model' => 'points-category',
            'level' => '积分管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Points\Models\Category'
        );
        $tree[] = $item;

        // 积分规则
        $item = array(
            'menu_name' => '积分规则',
            'menu_model' => 'points-rule',
            'level' => '积分管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Points\Models\Rule'
        );
        $tree[] = $item;

        // 积分用户
        $item = array(
            'menu_name' => '积分用户',
            'menu_model' => 'points-user',
            'level' => '积分管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Points\Models\User'
        );
        $tree[] = $item;


        // 积分日志
        $item = array(
            'menu_name' => '积分日志',
            'menu_model' => 'points-log',
            'level' => '积分管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Points\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
