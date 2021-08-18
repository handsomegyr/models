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

        // 服务管理
        $item = array(
            'menu_name' => '服务管理',
            'menu_model' => 'company-service',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\Service'
        );
        $tree[] = $item;

        // 团队管理
        $item = array(
            'menu_name' => '团队管理',
            'menu_model' => 'company-team',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\Team'
        );
        $tree[] = $item;

        // 团队用户管理
        $item = array(
            'menu_name' => '团队用户管理',
            'menu_model' => 'company-teamuser',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\TeamUser'
        );
        $tree[] = $item;

        // 团队用户工作管理
        $item = array(
            'menu_name' => '团队用户工作管理',
            'menu_model' => 'company-teamuserwork',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\TeamUserWork'
        );
        $tree[] = $item;

        // 组件管理
        $item = array(
            'menu_name' => '组件管理',
            'menu_model' => 'company-component',
            'level' => '公司CUT系统管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Company\Models\Component'
        );
        $tree[] = $item;

        return $tree;
    }
}
