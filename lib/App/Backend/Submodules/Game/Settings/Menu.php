<?php

namespace App\Backend\Submodules\Game\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 游戏管理 父节点
        $item = array(
            'menu_name' => '游戏管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 游戏
        $item = array(
            'menu_name' => '游戏',
            'menu_model' => 'game-game',
            'level' => '游戏管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Game\Models\Game'
        );
        $tree[] = $item;

        // 游戏玩家
        $item = array(
            'menu_name' => '游戏玩家',
            'menu_model' => 'game-user',
            'level' => '游戏管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Game\Models\User'
        );
        $tree[] = $item;

        // 游戏日志
        $item = array(
            'menu_name' => '游戏日志',
            'menu_model' => 'game-log',
            'level' => '游戏管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Game\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
