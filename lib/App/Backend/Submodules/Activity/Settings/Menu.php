<?php

namespace App\Backend\Submodules\Activity\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 活动管理 父节点
        $item = array(
            'menu_name' => '活动管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 活动分类
        $item = array(
            'menu_name' => '活动分类',
            'menu_model' => 'activity-category',
            'level' => '活动管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Activity\Models\Category'
        );
        $tree[] = $item;

        // 活动
        $item = array(
            'menu_name' => '活动',
            'menu_model' => 'activity-activity',
            'level' => '活动管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Activity\Models\Activity'
        );
        $tree[] = $item;

        // 活动用户
        $item = array(
            'menu_name' => '活动用户',
            'menu_model' => 'activity-user',
            'level' => '活动管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Activity\Models\User'
        );
        $tree[] = $item;

        // 活动黑名单用户
        $item = array(
            'menu_name' => '活动黑名单用户',
            'menu_model' => 'activity-blackuser',
            'level' => '活动管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Activity\Models\BlackUser'
        );
        $tree[] = $item;

        // 活动错误信息日志
        $item = array(
            'menu_name' => '活动错误信息日志',
            'menu_model' => 'activity-errorlog',
            'level' => '活动管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Activity\Models\ErrorLog'
        );
        $tree[] = $item;

        return $tree;
    }
}
