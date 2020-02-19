<?php

namespace App\Backend\Submodules\Vote\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 投票管理 父节点
        $item = array(
            'menu_name' => '投票管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 投票分类
        $item = array(
            'menu_name' => '投票分类',
            'menu_model' => 'vote-category',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\Category'
        );
        $tree[] = $item;

        // 投票限制类别
        $item = array(
            'menu_name' => '投票限制类别',
            'menu_model' => 'vote-limitcategory',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\LimitCategory'
        );
        $tree[] = $item;

        // 投票主题
        $item = array(
            'menu_name' => '投票主题',
            'menu_model' => 'vote-subject',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\Subject'
        );
        $tree[] = $item;

        // 投票主题选项
        $item = array(
            'menu_name' => '投票主题选项',
            'menu_model' => 'vote-item',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\Item'
        );
        $tree[] = $item;

        // 投票限制
        $item = array(
            'menu_name' => '投票限制',
            'menu_model' => 'vote-limit',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\Limit'
        );
        $tree[] = $item;

        // 投票日志
        $item = array(
            'menu_name' => '投票日志',
            'menu_model' => 'vote-log',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\Log'
        );
        $tree[] = $item;

        // 投票排行期
        $item = array(
            'menu_name' => '投票排行期',
            'menu_model' => 'vote-period',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\Period'
        );
        $tree[] = $item;

        // 投票每期排行
        $item = array(
            'menu_name' => '投票每期排行',
            'menu_model' => 'vote-rankperiod',
            'level' => '投票管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Vote\Models\RankPeriod'
        );
        $tree[] = $item;

        return $tree;
    }
}
