<?php

namespace App\Backend\Submodules\Task\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 任务管理 父节点
        $item = array(
            'menu_name' => '任务管理',
            'menu_model' => '',
            'level' => '',
            'icon' => 'images',
            'model' => ''
        );
        $tree[] = $item;

        // 任务
        $item = array(
            'menu_name' => '任务',
            'menu_model' => 'task-task',
            'level' => '任务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Task\Models\Task'
        );
        $tree[] = $item;

        // 任务日志
        $item = array(
            'menu_name' => '任务日志',
            'menu_model' => 'task-log',
            'level' => '任务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Task\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
