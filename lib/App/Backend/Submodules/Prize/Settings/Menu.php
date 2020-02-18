<?php

namespace App\Backend\Submodules\Prize\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 奖品管理 父节点
        $item = array(
            'menu_name' => '奖品管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 奖品分类
        $item = array(
            'menu_name' => '奖品分类',
            'menu_model' => 'prize-category',
            'level' => '奖品管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Prize\Models\Category'
        );
        $tree[] = $item;

        // 奖品
        $item = array(
            'menu_name' => '奖品',
            'menu_model' => 'prize-prize',
            'level' => '奖品管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Prize\Models\Prize'
        );
        $tree[] = $item;

        // 奖品券码
        $item = array(
            'menu_name' => '奖品券码',
            'menu_model' => 'prize-code',
            'level' => '奖品管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Prize\Models\Code'
        );
        $tree[] = $item;

        return $tree;
    }
}
