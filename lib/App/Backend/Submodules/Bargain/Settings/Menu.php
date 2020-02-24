<?php

namespace App\Backend\Submodules\Bargain\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 砍价管理 父节点
        $item = array(
            'menu_name' => '砍价管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 砍价物
        $item = array(
            'menu_name' => '砍价物',
            'menu_model' => 'bargain-bargain',
            'level' => '砍价管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Bargain\Models\Bargain'
        );
        $tree[] = $item;

        // 砍价用户系数
        $item = array(
            'menu_name' => '砍价用户系数',
            'menu_model' => 'bargain-alphauser',
            'level' => '砍价管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Bargain\Models\AlphaUser'
        );
        $tree[] = $item;

        // 砍价用户惩罚系数
        $item = array(
            'menu_name' => '砍价用户惩罚系数',
            'menu_model' => 'bargain-blackuser',
            'level' => '砍价管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Bargain\Models\BlackUser'
        );
        $tree[] = $item;

        // 砍价日志
        $item = array(
            'menu_name' => '砍价日志',
            'menu_model' => 'bargain-log',
            'level' => '砍价管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Bargain\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
