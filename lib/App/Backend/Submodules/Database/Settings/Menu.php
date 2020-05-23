<?php

namespace App\Backend\Submodules\Database\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // iDB管理 父节点
        $item = array(
            'menu_name' => 'iDB管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 数据库管理
        $item = array(
            'menu_name' => '数据库管理',
            'menu_model' => 'database-project',
            'level' => 'iDB管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project'
        );
        $tree[] = $item;

        // 插件管理
        $item = array(
            'menu_name' => '插件管理',
            'menu_model' => 'database-plugin',
            'level' => 'iDB管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Plugin'
        );
        $tree[] = $item;

        // 参数配置管理
        $item = array(
            'menu_name' => '参数配置管理',
            'menu_model' => 'database-setting',
            'level' => 'iDB管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Setting'
        );
        $tree[] = $item;

        return $tree;
    }
}
