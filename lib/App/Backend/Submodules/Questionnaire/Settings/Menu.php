<?php

namespace App\Backend\Submodules\Questionnaire\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 问卷管理 父节点
        $item = array(
            'menu_name' => '问卷管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 问卷题型
        $item = array(
            'menu_name' => '题目题型',
            'menu_model' => 'questionnaire-questiontype',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\QuestionType'
        );
        $tree[] = $item;

        // 问卷
        $item = array(
            'menu_name' => '问卷',
            'menu_model' => 'questionnaire-questionnaire',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\Questionnaire'
        );
        $tree[] = $item;

        // 问卷题目管理
        $item = array(
            'menu_name' => '问卷题目',
            'menu_model' => 'questionnaire-question',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\Question'
        );
        $tree[] = $item;

        // 题目选项
        $item = array(
            'menu_name' => '题目选项',
            'menu_model' => 'questionnaire-questionitem',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\QuestionItem'
        );
        $tree[] = $item;

        // 随机题库管理
        $item = array(
            'menu_name' => '随机题库',
            'menu_model' => 'questionnaire-random',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\Random'
        );
        $tree[] = $item;

        // 问卷答案
        $item = array(
            'menu_name' => '问卷答案',
            'menu_model' => 'questionnaire-answer',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\Answer'
        );
        $tree[] = $item;

        // 题目展现方式
        $item = array(
            'menu_name' => '题目展现方式',
            'menu_model' => 'questionnaire-questionshowtype',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\QuestionShowType'
        );
        $tree[] = $item;

        // 题目分类
        $item = array(
            'menu_name' => '题目分类',
            'menu_model' => 'questionnaire-questioncategory',
            'level' => '问卷管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Questionnaire\Models\QuestionCategory'
        );
        $tree[] = $item;

        return $tree;
    }
}
