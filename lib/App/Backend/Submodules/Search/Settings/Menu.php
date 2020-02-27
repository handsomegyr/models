<?php

namespace App\Backend\Submodules\Search\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 搜索管理 父节点
        $item = array(
            'menu_name' => '搜索管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 搜索关键词
        $item = array(
            'menu_name' => '搜索关键词',
            'menu_model' => 'search-keyword',
            'level' => '搜索管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Search\Models\Keyword'
        );
        $tree[] = $item;


        // 搜索日志
        $item = array(
            'menu_name' => '搜索日志',
            'menu_model' => 'search-log',
            'level' => '搜索管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Search\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
