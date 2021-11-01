<?php

namespace App\Backend\Submodules\Tag\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 标签管理 父节点
        $item = array(
            'menu_name' => '标签管理',
            'menu_model' => '',
            'level' => '',
            'icon' => 'images',
            'model' => ''
        );
        $tree[] = $item;

        // 标签分类
        $item = array(
            'menu_name' => '标签分类',
            'menu_model' => 'tag-category',
            'level' => '标签管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Tag\Models\Category'
        );
        $tree[] = $item;

        // 标签
        $item = array(
            'menu_name' => '标签',
            'menu_model' => 'tag-tag',
            'level' => '标签管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Tag\Models\Tag'
        );
        $tree[] = $item;

        // 标签和实体的对应表
        $item = array(
            'menu_name' => '给实体打标签',
            'menu_model' => 'tag-tagtoentity',
            'level' => '标签管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Tag\Models\TagToEntity'
        );
        $tree[] = $item;

        return $tree;
    }
}
