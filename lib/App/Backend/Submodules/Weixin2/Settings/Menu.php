<?php

namespace App\Backend\Submodules\Weixin2\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 微信平台管理 父节点
        $item = array(
            'menu_name' => '微信平台管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 平台应用管理
        $item = array(
            'menu_name' => '平台应用管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 第三方平台设置
        $item = array(
            'menu_name' => '第三方平台设置',
            'menu_model' => 'weixin2-component',
            'level' => '平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Component\Component'
        );
        $tree[] = $item;

        // 授权方设置
        $item = array(
            'menu_name' => '授权方设置',
            'menu_model' => 'weixin2-authorizer',
            'level' => '平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer'
        );
        $tree[] = $item;

        // 登录授权发起执行时间跟踪统计
        $item = array(
            'menu_name' => '登录授权发起执行时间跟踪统计',
            'menu_model' => 'weixin2-componentloginbindtracking',
            'level' => '平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Component\ComponentLoginBindTracking'
        );
        $tree[] = $item;

        // 授权事件接收日志
        $item = array(
            'menu_name' => '授权事件接收日志',
            'menu_model' => 'weixin2-authorizelog',
            'level' => '平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Authorize\AuthorizeLog'
        );
        $tree[] = $item;

        // 授权管理
        $item = array(
            'menu_name' => '授权管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 授权应用设置
        $item = array(
            'menu_name' => '授权应用设置',
            'menu_model' => 'weixin2-snsapplication',
            'level' => '授权管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\SnsApplication'
        );
        $tree[] = $item;

        // 回调地址安全域名
        $item = array(
            'menu_name' => '回调地址安全域名',
            'menu_model' => 'weixin2-callbackurls',
            'level' => '授权管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Callbackurls'
        );
        $tree[] = $item;

        // 授权执行时间跟踪统计
        $item = array(
            'menu_name' => '授权执行时间跟踪统计',
            'menu_model' => 'weixin2-scripttracking',
            'level' => '授权管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\ScriptTracking'
        );
        $tree[] = $item;

        // 平台用户管理
        $item = array(
            'menu_name' => '平台用户管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 用户
        $item = array(
            'menu_name' => '用户',
            'menu_model' => 'weixin2-user',
            'level' => '平台用户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\User\User'
        );
        $tree[] = $item;

        // 用户标签
        $item = array(
            'menu_name' => '用户标签',
            'menu_model' => 'weixin2-usertag',
            'level' => '平台用户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\User\Tag'
        );
        $tree[] = $item;

        // 用户和用户标签对应设置
        $item = array(
            'menu_name' => '用户和用户标签对应设置',
            'menu_model' => 'weixin2-usertousertag',
            'level' => '平台用户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\User\UserToUserTag'
        );
        $tree[] = $item;

        // 黑名单
        $item = array(
            'menu_name' => '黑名单',
            'menu_model' => 'weixin2-blackuser',
            'level' => '平台用户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\User\BlackUser'
        );
        $tree[] = $item;

        // 关注用户
        $item = array(
            'menu_name' => '关注用户',
            'menu_model' => 'weixin2-subscribeuser',
            'level' => '平台用户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\User\SubscribeUser'
        );
        $tree[] = $item;

        // 消息相关管理
        $item = array(
            'menu_name' => '消息相关管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 消息与事件接收日志
        $item = array(
            'menu_name' => '消息与事件接收日志',
            'menu_model' => 'weixin2-msglog',
            'level' => '消息相关管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Msg\Log'
        );
        $tree[] = $item;

        // 被动回复用户消息管理
        $item = array(
            'menu_name' => '被动回复用户消息管理',
            'menu_model' => '',
            'level' => '消息相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 客服管理
        $item = array(
            'menu_name' => '客服管理',
            'menu_model' => '',
            'level' => '消息相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 模板消息管理
        $item = array(
            'menu_name' => '模板消息管理',
            'menu_model' => '',
            'level' => '消息相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 一次性订阅消息管理
        $item = array(
            'menu_name' => '一次性订阅消息管理',
            'menu_model' => '',
            'level' => '消息相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 群发管理
        $item = array(
            'menu_name' => '群发管理',
            'menu_model' => '',
            'level' => '消息相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 被动回复用户消息设定
        $item = array(
            'menu_name' => '被动回复用户消息设定',
            'menu_model' => 'weixin2-replymsg',
            'level' => '被动回复用户消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\ReplyMsg\ReplyMsg'
        );
        $tree[] = $item;

        // 被动回复用户消息图文设置
        $item = array(
            'menu_name' => '被动回复用户消息图文设置',
            'menu_model' => 'weixin2-replymsgnews',
            'level' => '被动回复用户消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\ReplyMsg\News'
        );
        $tree[] = $item;

        // 被动回复用户消息发送日志
        $item = array(
            'menu_name' => '被动回复用户消息发送日志',
            'menu_model' => 'weixin2-replymsgsendlog',
            'level' => '被动回复用户消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\ReplyMsg\SendLog'
        );
        $tree[] = $item;

        // 客服账号
        $item = array(
            'menu_name' => '客服账号',
            'menu_model' => 'weixin2-kfaccount',
            'level' => '客服管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Kf\Account'
        );
        $tree[] = $item;

        // 客服会话
        $item = array(
            'menu_name' => '客服会话',
            'menu_model' => 'weixin2-kfsession',
            'level' => '客服管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Kf\Session'
        );
        $tree[] = $item;

        // 客服消息
        $item = array(
            'menu_name' => '客服消息',
            'menu_model' => 'weixin2-custommsg',
            'level' => '客服管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\CustomMsg\CustomMsg'
        );
        $tree[] = $item;

        // 客服消息图文设置
        $item = array(
            'menu_name' => '客服消息图文设置',
            'menu_model' => 'weixin2-custommsgnews',
            'level' => '客服管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\CustomMsg\News'
        );
        $tree[] = $item;

        // 客服消息发送日志
        $item = array(
            'menu_name' => '客服消息发送日志',
            'menu_model' => 'weixin2-custommsgsendlog',
            'level' => '客服管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\CustomMsg\SendLog'
        );
        $tree[] = $item;

        // 聊天记录
        $item = array(
            'menu_name' => '聊天记录',
            'menu_model' => 'weixin2-msgrecord',
            'level' => '客服管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Kf\MsgRecord'
        );
        $tree[] = $item;

        // 模板管理
        $item = array(
            'menu_name' => '模板管理',
            'menu_model' => 'weixin2-template',
            'level' => '模板消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Template\Template'
        );
        $tree[] = $item;

        // 模板消息
        $item = array(
            'menu_name' => '模板消息',
            'menu_model' => 'weixin2-templatemsg',
            'level' => '模板消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\TemplateMsg\TemplateMsg'
        );
        $tree[] = $item;

        // 模板消息发送日志
        $item = array(
            'menu_name' => '模板消息发送日志',
            'menu_model' => 'weixin2-templatemsgsendlog',
            'level' => '模板消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\TemplateMsg\SendLog'
        );
        $tree[] = $item;

        // 一次性订阅消息订阅日志
        $item = array(
            'menu_name' => '一次性订阅消息订阅日志',
            'menu_model' => 'weixin2-subscribemsgsubscribelog',
            'level' => '一次性订阅消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\SubscribeMsg\SubscribeLog'
        );
        $tree[] = $item;

        // 群发消息
        $item = array(
            'menu_name' => '群发消息',
            'menu_model' => 'weixin2-massmsg',
            'level' => '群发管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\MassMsg\MassMsg'
        );
        $tree[] = $item;

        // 群发消息图文设置
        $item = array(
            'menu_name' => '群发消息图文设置',
            'menu_model' => 'weixin2-massmsgnews',
            'level' => '群发管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\MassMsg\News'
        );
        $tree[] = $item;

        // 群发消息发送日志
        $item = array(
            'menu_name' => '群发消息发送日志',
            'menu_model' => 'weixin2-massmsgsendlog',
            'level' => '群发管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\MassMsg\SendLog'
        );
        $tree[] = $item;

        // 已群发文章评论
        $item = array(
            'menu_name' => '已群发文章评论',
            'menu_model' => 'weixin2-comment',
            'level' => '群发管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Comment\Comment'
        );
        $tree[] = $item;

        // 已群发文章评论日志
        $item = array(
            'menu_name' => '已群发文章评论日志',
            'menu_model' => 'weixin2-commentlog',
            'level' => '群发管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Comment\Log'
        );
        $tree[] = $item;

        // 已群发文章评论回复日志
        $item = array(
            'menu_name' => '已群发文章评论回复日志',
            'menu_model' => 'weixin2-commentreplylog',
            'level' => '群发管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Comment\ReplyLog'
        );
        $tree[] = $item;

        // 自定义菜单相关管理
        $item = array(
            'menu_name' => '自定义菜单相关管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 自定义菜单设置
        $item = array(
            'menu_name' => '自定义菜单设置',
            'menu_model' => 'weixin2-menu',
            'level' => '自定义菜单相关管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Menu\Menu'
        );
        $tree[] = $item;

        // 个性化菜单匹配规则设置
        $item = array(
            'menu_name' => '个性化菜单匹配规则设置',
            'menu_model' => 'weixin2-menuconditionalmatchrule',
            'level' => '自定义菜单相关管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Menu\ConditionalMatchrule'
        );
        $tree[] = $item;

        // 个性化菜单设置
        $item = array(
            'menu_name' => '个性化菜单设置',
            'menu_model' => 'weixin2-menuconditional',
            'level' => '自定义菜单相关管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Menu\Conditional'
        );
        $tree[] = $item;

        // 二维码相关管理
        $item = array(
            'menu_name' => '二维码相关管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 公众号二维码管理
        $item = array(
            'menu_name' => '公众号二维码管理',
            'menu_model' => '',
            'level' => '二维码相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 小程序二维码管理
        $item = array(
            'menu_name' => '小程序二维码管理',
            'menu_model' => '',
            'level' => '二维码相关管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 公众号二维码
        $item = array(
            'menu_name' => '公众号二维码',
            'menu_model' => 'weixin2-qrcode',
            'level' => '公众号二维码管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Qrcode\Qrcode'
        );
        $tree[] = $item;

        // 公众号二维码事件推送日志
        $item = array(
            'menu_name' => '公众号二维码事件推送日志',
            'menu_model' => 'weixin2-qrcodeeventlog',
            'level' => '公众号二维码管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Qrcode\EventLog'
        );
        $tree[] = $item;

        // 小程序二维码
        $item = array(
            'menu_name' => '小程序二维码',
            'menu_model' => 'weixin2-miniprogramqrcode',
            'level' => '小程序二维码管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\Qrcode\Qrcode'
        );
        $tree[] = $item;

        // 小程序二维码扫描日志
        $item = array(
            'menu_name' => '小程序二维码扫描日志',
            'menu_model' => 'weixin2-miniprogramqrcodelog',
            'level' => '小程序二维码管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\Qrcode\Log'
        );
        $tree[] = $item;

        // 小程序URL链接
        $item = array(
            'menu_name' => '小程序URL链接',
            'menu_model' => 'weixin2-miniprogramurllink',
            'level' => '小程序二维码管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\Urllink'
        );
        $tree[] = $item;

        // 小程序scheme码
        $item = array(
            'menu_name' => '小程序scheme码',
            'menu_model' => 'weixin2-miniprogramurlscheme',
            'level' => '小程序二维码管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\Urlscheme'
        );
        $tree[] = $item;

        // 素材管理
        $item = array(
            'menu_name' => '素材管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 永久素材管理
        $item = array(
            'menu_name' => '永久素材管理',
            'menu_model' => '',
            'level' => '素材管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 临时素材管理
        $item = array(
            'menu_name' => '临时素材管理',
            'menu_model' => '',
            'level' => '素材管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 永久素材设置
        $item = array(
            'menu_name' => '永久素材设置',
            'menu_model' => 'weixin2-material',
            'level' => '永久素材管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Material\Material'
        );
        $tree[] = $item;

        // 永久图文素材设置
        $item = array(
            'menu_name' => '永久图文素材设置',
            'menu_model' => 'weixin2-materialnews',
            'level' => '永久素材管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Material\News'
        );
        $tree[] = $item;

        // 临时素材设置
        $item = array(
            'menu_name' => '临时素材设置',
            'menu_model' => 'weixin2-media',
            'level' => '临时素材管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Media\Media'
        );
        $tree[] = $item;

        // 系统配置管理
        $item = array(
            'menu_name' => '系统配置管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 素材类型
        $item = array(
            'menu_name' => '素材类型',
            'menu_model' => 'weixin2-mediatype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Media\Type'
        );
        $tree[] = $item;

        // 消息类型
        $item = array(
            'menu_name' => '消息类型',
            'menu_model' => 'weixin2-msgtype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Msg\Type'
        );
        $tree[] = $item;

        // 回复消息类型
        $item = array(
            'menu_name' => '回复消息类型',
            'menu_model' => 'weixin2-replymsgtype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\ReplyMsg\Type'
        );
        $tree[] = $item;

        // 群发消息类型
        $item = array(
            'menu_name' => '群发消息类型',
            'menu_model' => 'weixin2-massmsgtype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\MassMsg\Type'
        );
        $tree[] = $item;

        // 群发消息发送方式
        $item = array(
            'menu_name' => '群发消息发送方式',
            'menu_model' => 'weixin2-massmsgsendmethod',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\MassMsg\SendMethod'
        );
        $tree[] = $item;

        // 客服消息类型
        $item = array(
            'menu_name' => '客服消息类型',
            'menu_model' => 'weixin2-custommsgtype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\CustomMsg\Type'
        );
        $tree[] = $item;

        // 自定义菜单类型
        $item = array(
            'menu_name' => '自定义菜单类型',
            'menu_model' => 'weixin2-menutype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Menu\Type'
        );
        $tree[] = $item;

        // 公众号二维码类型
        $item = array(
            'menu_name' => '公众号二维码类型',
            'menu_model' => 'weixin2-qrcodetype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Qrcode\Type'
        );
        $tree[] = $item;

        // 小程序二维码类型
        $item = array(
            'menu_name' => '小程序二维码类型',
            'menu_model' => 'weixin2-miniprogramqrcodetype',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\Qrcode\Type'
        );
        $tree[] = $item;

        // 用户关注渠道来源
        $item = array(
            'menu_name' => '用户关注渠道来源',
            'menu_model' => 'weixin2-subscribescene',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\SubscribeScene'
        );
        $tree[] = $item;

        // 语言
        $item = array(
            'menu_name' => '语言',
            'menu_model' => 'weixin2-language',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Language'
        );
        $tree[] = $item;

        // 事件分类
        $item = array(
            'menu_name' => '事件分类',
            'menu_model' => 'weixin2-eventcategory',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Event\Category'
        );
        $tree[] = $item;

        // 事件
        $item = array(
            'menu_name' => '事件',
            'menu_model' => 'weixin2-event',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Event\Event'
        );
        $tree[] = $item;

        // 用户渠道来源
        $item = array(
            'menu_name' => '用户渠道来源',
            'menu_model' => 'weixin2-usersource',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\User\Source'
        );
        $tree[] = $item;

        // 数据分时
        $item = array(
            'menu_name' => '数据分时',
            'menu_model' => 'weixin2-refhour',
            'level' => '系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\RefHour'
        );
        $tree[] = $item;

        // 服务管理
        $item = array(
            'menu_name' => '服务管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 服务设置
        $item = array(
            'menu_name' => '服务设置',
            'menu_model' => 'weixin2-service',
            'level' => '服务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Service'
        );
        $tree[] = $item;

        // 长链接转短链接
        $item = array(
            'menu_name' => '长链接转短链接',
            'menu_model' => 'weixin2-shorturl',
            'level' => '服务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Shorturl'
        );
        $tree[] = $item;

        // 关键词回复管理
        $item = array(
            'menu_name' => '关键词回复管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 关键字设定
        $item = array(
            'menu_name' => '关键字设定',
            'menu_model' => 'weixin2-keyword',
            'level' => '关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Keyword\Keyword'
        );
        $tree[] = $item;

        // 非关键词
        $item = array(
            'menu_name' => '非关键词',
            'menu_model' => 'weixin2-word',
            'level' => '关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Keyword\Word'
        );
        $tree[] = $item;

        // 关键词和回复消息对应设定
        $item = array(
            'menu_name' => '关键词和回复消息对应设定',
            'menu_model' => 'weixin2-keywordtoreplymsg',
            'level' => '关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToReplyMsg'
        );
        $tree[] = $item;

        // 关键词和客服消息对应设定
        $item = array(
            'menu_name' => '关键词和客服消息对应设定',
            'menu_model' => 'weixin2-keywordtocustommsg',
            'level' => '关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToCustomMsg'
        );
        $tree[] = $item;

        // 关键词和模板消息对应设定
        $item = array(
            'menu_name' => '关键词和模板消息对应设定',
            'menu_model' => 'weixin2-keywordtotemplatemsg',
            'level' => '关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToTemplateMsg'
        );
        $tree[] = $item;

        // 关键词和服务对应设定
        $item = array(
            'menu_name' => '关键词和服务对应设定',
            'menu_model' => 'weixin2-keywordtoservice',
            'level' => '关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Keyword\KeywordToService'
        );
        $tree[] = $item;

        // 数据统计管理
        $item = array(
            'menu_name' => '数据统计管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 用户分析数据统计
        $item = array(
            'menu_name' => '用户分析数据统计',
            'menu_model' => '',
            'level' => '数据统计管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 图文分析数据统计
        $item = array(
            'menu_name' => '图文分析数据统计',
            'menu_model' => '',
            'level' => '数据统计管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 消息分析数据统计
        $item = array(
            'menu_name' => '消息分析数据统计',
            'menu_model' => '',
            'level' => '数据统计管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 接口分析数据统计
        $item = array(
            'menu_name' => '接口分析数据统计',
            'menu_model' => '',
            'level' => '数据统计管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 用户增减数据
        $item = array(
            'menu_name' => '用户增减数据',
            'menu_model' => 'weixin2-datacubeusersummary',
            'level' => '用户分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UserSummary'
        );
        $tree[] = $item;

        // 累计用户数据
        $item = array(
            'menu_name' => '累计用户数据',
            'menu_model' => 'weixin2-datacubeusercumulate',
            'level' => '用户分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UserCumulate'
        );
        $tree[] = $item;

        // 图文群发每日数据
        $item = array(
            'menu_name' => '图文群发每日数据',
            'menu_model' => 'weixin2-datacubearticlesummary',
            'level' => '图文分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\ArticleSummary'
        );
        $tree[] = $item;

        // 图文群发总数据
        $item = array(
            'menu_name' => '图文群发总数据',
            'menu_model' => 'weixin2-datacubearticletotal',
            'level' => '图文分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\ArticleTotal'
        );
        $tree[] = $item;

        // 图文统计数据
        $item = array(
            'menu_name' => '图文统计数据',
            'menu_model' => 'weixin2-datacubeuserread',
            'level' => '图文分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UserRead'
        );
        $tree[] = $item;

        // 图文统计分时数据
        $item = array(
            'menu_name' => '图文统计分时数据',
            'menu_model' => 'weixin2-datacubeuserreadhour',
            'level' => '图文分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UserReadHour'
        );
        $tree[] = $item;

        // 图文分享转发数据
        $item = array(
            'menu_name' => '图文分享转发数据',
            'menu_model' => 'weixin2-datacubeusershare',
            'level' => '图文分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UserShare'
        );
        $tree[] = $item;

        // 图文分享转发分时数据
        $item = array(
            'menu_name' => '图文分享转发分时数据',
            'menu_model' => 'weixin2-datacubeusersharehour',
            'level' => '图文分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UserShareHour'
        );
        $tree[] = $item;

        // 消息发送概况数据
        $item = array(
            'menu_name' => '消息发送概况数据',
            'menu_model' => 'weixin2-datacubeupstreammsg',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsg'
        );
        $tree[] = $item;

        // 消息分送分时数据
        $item = array(
            'menu_name' => '消息分送分时数据',
            'menu_model' => 'weixin2-datacubeupstreammsghour',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgHour'
        );
        $tree[] = $item;

        // 消息发送周数据
        $item = array(
            'menu_name' => '消息发送周数据',
            'menu_model' => 'weixin2-datacubeupstreammsgweek',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgWeek'
        );
        $tree[] = $item;

        // 消息发送月数据
        $item = array(
            'menu_name' => '消息发送月数据',
            'menu_model' => 'weixin2-datacubeupstreammsgmonth',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgMonth'
        );
        $tree[] = $item;

        // 消息发送分布数据
        $item = array(
            'menu_name' => '消息发送分布数据',
            'menu_model' => 'weixin2-datacubeupstreammsgdist',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgDist'
        );
        $tree[] = $item;

        // 消息发送分布分时数据
        $item = array(
            'menu_name' => '消息发送分布分时数据',
            'menu_model' => 'weixin2-datacubeupstreammsgdisthour',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgDistHour'
        );
        $tree[] = $item;

        // 消息发送分布周数据
        $item = array(
            'menu_name' => '消息发送分布周数据',
            'menu_model' => 'weixin2-datacubeupstreammsgdistweek',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgDistWeek'
        );
        $tree[] = $item;

        // 消息发送分布月数据
        $item = array(
            'menu_name' => '消息发送分布月数据',
            'menu_model' => 'weixin2-datacubeupstreammsgdistmonth',
            'level' => '消息分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\UpstreamMsgDistMonth'
        );
        $tree[] = $item;

        // 接口分析数据
        $item = array(
            'menu_name' => '接口分析数据',
            'menu_model' => 'weixin2-datacubeinterfacesummary',
            'level' => '接口分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\InterfaceSummary'
        );
        $tree[] = $item;

        // 接口分析分时数据
        $item = array(
            'menu_name' => '接口分析分时数据',
            'menu_model' => 'weixin2-datacubeinterfacesummaryhour',
            'level' => '接口分析数据统计',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\DataCube\InterfaceSummaryHour'
        );
        $tree[] = $item;

        // 消息推送管理
        $item = array(
            'menu_name' => '消息推送管理',
            'menu_model' => '',
            'level' => '微信平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 推送任务
        $item = array(
            'menu_name' => '推送任务',
            'menu_model' => 'weixin2-notificationtask',
            'level' => '消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Notification\Task'
        );
        $tree[] = $item;

        // 推送任务内容
        $item = array(
            'menu_name' => '推送任务内容',
            'menu_model' => 'weixin2-notificationtaskcontent',
            'level' => '消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Notification\TaskContent'
        );
        $tree[] = $item;

        // 推送任务日志
        $item = array(
            'menu_name' => '推送任务日志',
            'menu_model' => 'weixin2-notificationtasklog',
            'level' => '消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Notification\TaskLog'
        );
        $tree[] = $item;

        // 推送任务处理
        $item = array(
            'menu_name' => '推送任务处理',
            'menu_model' => 'weixin2-notificationtaskprocess',
            'level' => '消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Notification\TaskProcess'
        );
        $tree[] = $item;

        // 小程序消息管理
        $item = array(
            'menu_name' => '小程序消息管理',
            'menu_model' => '',
            'level' => '微信开放平台管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 小程序订阅消息模板
        $item = array(
            'menu_name' => '小程序订阅消息模板',
            'menu_model' => 'weixin2-miniprogramsubscribemsgtemplate',
            'level' => '小程序消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\SubscribeMsg\Template\Template'
        );
        $tree[] = $item;

        // 小程序订阅消息
        $item = array(
            'menu_name' => '小程序订阅消息',
            'menu_model' => 'weixin2-miniprogramsubscribemsg',
            'level' => '小程序消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\SubscribeMsg\Msg'
        );
        $tree[] = $item;

        // 小程序订阅消息发送日志
        $item = array(
            'menu_name' => '小程序订阅消息发送日志',
            'menu_model' => 'weixin2-miniprogramsubscribemsgsendlog',
            'level' => '小程序消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Weixin2\Models\Miniprogram\SubscribeMsg\SendLog'
        );
        $tree[] = $item;

        return $tree;
    }
}
