<?php

namespace App\Backend\Submodules\Lottery\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 抽奖管理 父节点
        $item = array(
            'menu_name' => '抽奖管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 抽奖规则
        $item = array(
            'menu_name' => '抽奖规则',
            'menu_model' => 'lottery-rule',
            'level' => '抽奖管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lottery\Models\Rule'
        );
        $tree[] = $item;

        // 抽奖限制
        $item = array(
            'menu_name' => '抽奖限制',
            'menu_model' => 'lottery-limit',
            'level' => '抽奖管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lottery\Models\Limit'
        );
        $tree[] = $item;

        // 中奖记录
        $item = array(
            'menu_name' => '中奖记录',
            'menu_model' => 'lottery-exchange',
            'level' => '抽奖管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lottery\Models\Exchange'
        );
        $tree[] = $item;

        // 抽奖日志
        $item = array(
            'menu_name' => '抽奖日志',
            'menu_model' => 'lottery-record',
            'level' => '抽奖管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Lottery\Models\Record'
        );
        $tree[] = $item;

        return $tree;
    }
}
