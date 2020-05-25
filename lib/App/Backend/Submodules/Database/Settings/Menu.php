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
            'menu_model' => '',
            'level' => 'iDB管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project'
        );
        $tree[] = $item;

        // 插件管理
        $item = array(
            'menu_name' => '插件管理',
            'menu_model' => '',
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

        // 数据库管理
        $item = array(
            'menu_name' => '数据库管理',
            'menu_model' => 'database-project',
            'level' => '数据库管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project'
        );
        $tree[] = $item;

        // 表管理
        $item = array(
            'menu_name' => '表管理',
            'menu_model' => '',
            'level' => '数据库管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection'
        );
        $tree[] = $item;

        // SN管理
        $item = array(
            'menu_name' => 'SN管理',
            'menu_model' => 'database-projectsn',
            'level' => '数据库管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Sn'
        );
        $tree[] = $item;

        // 数据库插件对应管理
        $item = array(
            'menu_name' => '数据库插件对应管理',
            'menu_model' => 'database-projectplugin',
            'level' => '数据库管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Plugin'
        );
        $tree[] = $item;

        // 表管理
        $item = array(
            'menu_name' => '表管理',
            'menu_model' => 'database-projectcollection',
            'level' => '表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection'
        );
        $tree[] = $item;

        // 表结构管理
        $item = array(
            'menu_name' => '表结构管理',
            'menu_model' => 'database-projectcollectionstructure',
            'level' => '表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection\Structure'
        );
        $tree[] = $item;

        // 表索引管理
        $item = array(
            'menu_name' => '表索引管理',
            'menu_model' => 'database-projectcollectionindex',
            'level' => '表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection\Index'
        );
        $tree[] = $item;

        // 表映射管理
        $item = array(
            'menu_name' => '表映射管理',
            'menu_model' => 'database-projectcollectionmapping',
            'level' => '表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection\Mapping'
        );
        $tree[] = $item;

        // 表排序管理
        $item = array(
            'menu_name' => '表排序管理',
            'menu_model' => 'database-projectcollectionorderby',
            'level' => '表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection\Orderby'
        );
        $tree[] = $item;

        // 表SN管理
        $item = array(
            'menu_name' => '表SN管理',
            'menu_model' => 'database-projectcollectionsn',
            'level' => '表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Project\Collection\Sn'
        );
        $tree[] = $item;


        // 插件管理
        $item = array(
            'menu_name' => '插件管理',
            'menu_model' => 'database-plugin',
            'level' => '插件管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Plugin'
        );
        $tree[] = $item;

        // 插件表管理
        $item = array(
            'menu_name' => '插件表管理',
            'menu_model' => '',
            'level' => '插件管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Plugin\Collection'
        );
        $tree[] = $item;

        // 插件表管理
        $item = array(
            'menu_name' => '插件表管理',
            'menu_model' => 'database-plugincollection',
            'level' => '插件表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Plugin\Collection'
        );
        $tree[] = $item;

        // 插件表结构管理
        $item = array(
            'menu_name' => '插件表结构管理',
            'menu_model' => 'database-plugincollectionstructure',
            'level' => '插件表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Plugin\Collection\Structure'
        );
        $tree[] = $item;

        // 插件表索引管理
        $item = array(
            'menu_name' => '插件表索引管理',
            'menu_model' => 'database-plugincollectionindex',
            'level' => '插件表管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Database\Models\Plugin\Collection\Index'
        );
        $tree[] = $item;

        return $tree;
    }
}
