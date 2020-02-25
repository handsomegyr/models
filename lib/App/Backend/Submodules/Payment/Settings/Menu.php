<?php

namespace App\Backend\Submodules\Payment\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 支付管理 父节点
        $item = array(
            'menu_name' => '支付管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 支付方式
        $item = array(
            'menu_name' => '支付方式',
            'menu_model' => 'payment-payment',
            'level' => '支付管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Payment\Models\Payment'
        );
        $tree[] = $item;

        // 回调消息管理 子节点
        $item = array(
            'menu_name' => '回调消息管理',
            'menu_model' => '',
            'level' => '支付管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 回调日志
        $item = array(
            'menu_name' => '回调日志',
            'menu_model' => 'payment-notifylog',
            'level' => '回调消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Payment\Models\NotifyLog'
        );
        $tree[] = $item;

        // 微信支付回调日志
        $item = array(
            'menu_name' => '微信支付回调日志',
            'menu_model' => 'payment-weixinpaylog',
            'level' => '回调消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Payment\Models\WeixinPayLog'
        );
        $tree[] = $item;

        // 回调通知
        $item = array(
            'menu_name' => '回调通知',
            'menu_model' => 'payment-notify',
            'level' => '回调消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Payment\Models\Notify'
        );
        $tree[] = $item;

        // 支付日志
        $item = array(
            'menu_name' => '支付日志',
            'menu_model' => 'payment-log',
            'level' => '回调消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Payment\Models\Log'
        );
        $tree[] = $item;

        return $tree;
    }
}
