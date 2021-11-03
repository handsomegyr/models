<?php

namespace App\Backend\Submodules\Member\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 会员管理 父节点
        $item = array(
            'menu_name' => '会员管理',
            'menu_model' => '',
            'level' => '',
            'icon' => 'images',
            'model' => ''
        );
        $tree[] = $item;

        // 会员
        $item = array(
            'menu_name' => '会员',
            'menu_model' => 'member-member',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\Member'
        );
        $tree[] = $item;

        // 会员绑定
        $item = array(
            'menu_name' => '会员绑定',
            'menu_model' => 'member-bind',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\Bind'
        );
        $tree[] = $item;

        // // 小程序用户
        // $item = array(
        //     'menu_name' => '小程序用户',
        //     'menu_model' => 'member-weixinuser',
        //     'level' => '会员管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Member\Models\WeixinUser'
        // );
        // $tree[] = $item;

        // // 会员地址
        // $item = array(
        //     'menu_name' => '会员地址',
        //     'menu_model' => 'member-address',
        //     'level' => '会员管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Member\Models\Address'
        // );
        // $tree[] = $item;

        // // 会员优惠券
        // $item = array(
        //     'menu_name' => '会员优惠券',
        //     'menu_model' => 'member-coupon',
        //     'level' => '会员管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Member\Models\Coupon'
        // );
        // $tree[] = $item;

        // 会员完成任务管理
        $item = array(
            'menu_name' => '会员完成任务管理',
            'menu_model' => 'member-task',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\Task'
        );
        $tree[] = $item;

        // 会员行为统计管理
        $item = array(
            'menu_name' => '会员行为统计管理',
            'menu_model' => 'member-behaviorstat',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\BehaviorStat'
        );
        $tree[] = $item;

        // 会员行为每日统计管理
        $item = array(
            'menu_name' => '会员行为每日统计管理',
            'menu_model' => 'member-behaviordailystat',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\BehaviorDailyStat'
        );
        $tree[] = $item;

        // 会员行为日志管理
        $item = array(
            'menu_name' => '会员行为日志管理',
            'menu_model' => 'member-behaviorlog',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\BehaviorLog'
        );
        $tree[] = $item;

        // 会员标签
        $item = array(
            'menu_name' => '会员标签',
            'menu_model' => 'member-tag',
            'level' => '会员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Member\Models\Tag'
        );
        $tree[] = $item;

        return $tree;
    }
}
