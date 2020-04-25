<?php

namespace App\Backend\Submodules\Company\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 公司CUT系统管理 父节点
        $item = array(
            'menu_name' => '公司CUT系统管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 项目管理
        $item = array(
            'menu_name' => '项目管理',
            'menu_model' => 'company-project',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\Project'
        );
        $tree[] = $item;

        // 项目用户管理
        $item = array(
            'menu_name' => '项目用户管理',
            'menu_model' => 'company-projectuser',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\ProjectUser'
        );
        $tree[] = $item;

        return $tree;
    }
}
