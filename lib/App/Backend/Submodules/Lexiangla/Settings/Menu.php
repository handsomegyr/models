<?php

namespace App\Backend\Submodules\Lexiangla\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 腾讯乐享管理 父节点
        $item = array(
            'menu_name' => '腾讯乐享管理',
            'menu_model' => '',
            'level' => '',
            'icon' => 'images',
            'model' => ''
        );
        $tree[] = $item;

        // 腾讯乐享应用管理 父节点
        $item = array(
            'menu_name' => '腾讯乐享应用管理',
            'menu_model' => 'lexiangla-application',
            'level' => '腾讯乐享管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Application'
        );
        $tree[] = $item;

        // 腾讯乐享通讯录管理 父节点
        $item = array(
            'menu_name' => '腾讯乐享通讯录管理',
            'menu_model' => '',
            'level' => '腾讯乐享管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 乐享部门
        $item = array(
            'menu_name' => '乐享部门',
            'menu_model' => 'lexiangla-department',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\Department'
        );
        $tree[] = $item;

        // 乐享部门同步
        $item = array(
            'menu_name' => '乐享部门同步',
            'menu_model' => 'lexiangla-departmentsync',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\DepartmentSync'
        );
        $tree[] = $item;

        // 乐享标签
        $item = array(
            'menu_name' => '乐享标签',
            'menu_model' => 'lexiangla-tag',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\Tag'
        );
        $tree[] = $item;

        // 乐享标签同步
        $item = array(
            'menu_name' => '乐享标签同步',
            'menu_model' => 'lexiangla-tagsync',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\TagSync'
        );
        $tree[] = $item;

        // 乐享成员
        $item = array(
            'menu_name' => '乐享成员',
            'menu_model' => 'lexiangla-user',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\User'
        );
        $tree[] = $item;

        // // 乐享成员同步
        // $item = array(
        //     'menu_name' => '乐享成员同步',
        //     'menu_model' => 'lexiangla-usersync',
        //     'level' => '腾讯乐享通讯录管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\UserSync'
        // );
        // $tree[] = $item;

        // 乐享标签成员
        $item = array(
            'menu_name' => '乐享标签成员',
            'menu_model' => 'lexiangla-taguser',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\TagUser'
        );
        $tree[] = $item;

        // 乐享标签部门
        $item = array(
            'menu_name' => '乐享标签部门',
            'menu_model' => 'lexiangla-tagparty',
            'level' => '腾讯乐享通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lexiangla\Models\Contact\TagParty'
        );
        $tree[] = $item;

        return $tree;
    }
}
