<?php

namespace App\Backend\Submodules\Store\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 店铺管理 父节点
        $item = array(
            'menu_name' => '店铺管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 店铺
        $item = array(
            'menu_name' => '店铺',
            'menu_model' => 'store-store',
            'level' => '店铺管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Store\Models\Store'
        );
        $tree[] = $item;

        return $tree;
    }
}
