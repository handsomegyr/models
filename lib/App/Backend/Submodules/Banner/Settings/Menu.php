<?php

namespace App\Backend\Submodules\Banner\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // Banner管理 父节点
        $item = array(
            'menu_name' => 'Banner管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // Banner
        $item = array(
            'menu_name' => 'Banner',
            'menu_model' => 'banner-banner',
            'level' => 'Banner管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Banner\Models\Banner'
        );
        $tree[] = $item;

        // Banner子项目
        $item = array(
            'menu_name' => 'Banner子项目',
            'menu_model' => 'banner-item',
            'level' => 'Banner管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Banner\Models\Item'
        );
        $tree[] = $item;


        return $tree;
    }
}
