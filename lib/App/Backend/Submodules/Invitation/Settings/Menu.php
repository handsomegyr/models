<?php

namespace App\Backend\Submodules\Invitation\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 邀请管理 父节点
        $item = array(
            'menu_name' => '邀请管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 邀请记录
        $item = array(
            'menu_name' => '邀请记录',
            'menu_model' => 'invitation-invitation',
            'level' => '邀请管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Invitation\Models\Invitation'
        );
        $tree[] = $item;

        // 邀请领取日志
        $item = array(
            'menu_name' => '邀请领取日志',
            'menu_model' => 'invitation-invitationgotdetail',
            'level' => '邀请管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Invitation\Models\InvitationGotDetail'
        );
        $tree[] = $item;

        return $tree;
    }
}
