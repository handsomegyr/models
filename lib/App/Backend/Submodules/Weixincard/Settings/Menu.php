<?php

namespace App\Backend\Submodules\Weixincard\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 微信卡券管理 父节点
        $item = array(
            'menu_name' => '微信卡券管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 微信卡券分类
        $item = array(
            'menu_name' => '微信卡券分类',
            'menu_model' => 'weixincard-cardtype',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\CardType'
        );
        $tree[] = $item;

        // 颜色
        $item = array(
            'menu_name' => '颜色',
            'menu_model' => 'weixincard-color',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Color'
        );
        $tree[] = $item;

        // code码展示类型
        $item = array(
            'menu_name' => 'code码展示类型',
            'menu_model' => 'weixincard-codetype',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\CodeType'
        );
        $tree[] = $item;

        // 使用时间类型
        $item = array(
            'menu_name' => '使用时间类型',
            'menu_model' => 'weixincard-dateinfotype',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\DateInfoType'
        );
        $tree[] = $item;

        // 会员信息卡类型
        $item = array(
            'menu_name' => '会员信息卡类型',
            'menu_model' => 'weixincard-customfieldtype',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\CustomFieldType'
        );
        $tree[] = $item;

        // 商户LOGO
        $item = array(
            'menu_name' => '商户LOGO',
            'menu_model' => 'weixincard-logo',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Logo'
        );
        $tree[] = $item;

        // 测试白名单
        $item = array(
            'menu_name' => '测试白名单',
            'menu_model' => 'weixincard-testwhitelist',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Testwhitelist'
        );
        $tree[] = $item;

        // 门店
        $item = array(
            'menu_name' => '门店',
            'menu_model' => 'weixincard-poi',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Poi'
        );
        $tree[] = $item;

        // 卡券
        $item = array(
            'menu_name' => '卡券',
            'menu_model' => 'weixincard-card',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Card'
        );
        $tree[] = $item;

        // 自定义卡券导入
        $item = array(
            'menu_name' => '自定义卡券导入',
            'menu_model' => 'weixincard-codedeposit',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\CodeDeposit'
        );
        $tree[] = $item;

        // 卡券二维码
        $item = array(
            'menu_name' => '卡券二维码',
            'menu_model' => 'weixincard-qrcard',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Qrcard'
        );
        $tree[] = $item;

        // 卡包
        $item = array(
            'menu_name' => '卡包',
            'menu_model' => 'weixincard-cardbag',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\CardBag'
        );
        $tree[] = $item;

        // 卡券事件
        $item = array(
            'menu_name' => '卡券事件',
            'menu_model' => 'weixincard-event',
            'level' => '微信卡券管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixincard\Models\Event'
        );
        $tree[] = $item;

        return $tree;
    }
}
