<?php

namespace App\Backend\Submodules\Cronjob\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 计划任务管理 父节点
        $item = array(
            'menu_name' => '计划任务管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 任务管理
        $item = array(
            'menu_name' => '任务管理',
            'menu_model' => 'cronjob-job',
            'level' => '计划任务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\Job'
        );
        $tree[] = $item;

        // 日志管理
        $item = array(
            'menu_name' => '日志管理',
            'menu_model' => 'cronjob-log',
            'level' => '计划任务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\Log'
        );
        $tree[] = $item;

        // 临时任务
        $item = array(
            'menu_name' => '临时任务',
            'menu_model' => 'cronjob-task',
            'level' => '计划任务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\Task'
        );
        $tree[] = $item;

        // 运行状况
        $item = array(
            'menu_name' => '运行状况',
            'menu_model' => 'cronjob-inspire',
            'level' => '计划任务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\Inspire'
        );
        $tree[] = $item;

        // 数据导入
        $item = array(
            'menu_name' => '数据导入',
            'menu_model' => '',
            'level' => '计划任务管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 导入文件
        $item = array(
            'menu_name' => '导入文件',
            'menu_model' => 'cronjob-dataimportfile',
            'level' => '数据导入',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\DataImport\File'
        );
        $tree[] = $item;

        // 导入文件内容
        $item = array(
            'menu_name' => '导入文件内容',
            'menu_model' => 'cronjob-dataimportfilecontent',
            'level' => '数据导入',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\DataImport\FileContent'
        );
        $tree[] = $item;

        // 处理日志
        $item = array(
            'menu_name' => '处理日志',
            'menu_model' => 'cronjob-dataimportlog',
            'level' => '数据导入',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Cronjob\Models\DataImport\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
