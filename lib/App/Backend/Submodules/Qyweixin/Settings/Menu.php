<?php

namespace App\Backend\Submodules\Qyweixin\Settings;

class Menu
{

    public function getSettings()
    {
        $tree = array();

        // 企业微信管理 父节点
        $item = array(
            'menu_name' => '企业微信管理',
            'menu_model' => '',
            'level' => '',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业平台应用管理
        $item = array(
            'menu_name' => '企业平台应用管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 第三方服务商设置
        $item = array(
            'menu_name' => '第三方服务商设置',
            'menu_model' => 'qyweixin-provider',
            'level' => '企业平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Provider\Provider'
        );
        $tree[] = $item;

        // 授权方设置
        $item = array(
            'menu_name' => '授权方设置',
            'menu_model' => 'qyweixin-authorizer',
            'level' => '企业平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Authorize\Authorizer'
        );
        $tree[] = $item;

        // 运用设置
        $item = array(
            'menu_name' => '应用设置',
            'menu_model' => 'qyweixin-agent',
            'level' => '企业平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Agent\Agent'
        );
        $tree[] = $item;

        // 授权方企业信息
        $item = array(
            'menu_name' => '授权方企业信息',
            'menu_model' => 'qyweixin-authcorp',
            'level' => '企业平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Auth\AuthCorp'
        );
        $tree[] = $item;

        // 登录授权发起执行时间跟踪统计
        $item = array(
            'menu_name' => '登录授权发起执行时间跟踪统计',
            'menu_model' => 'qyweixin-providerloginbindtracking',
            'level' => '企业平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Provider\ProviderLoginBindTracking'
        );
        $tree[] = $item;

        // 授权事件接收日志
        $item = array(
            'menu_name' => '授权事件接收日志',
            'menu_model' => 'qyweixin-authorizelog',
            'level' => '企业平台应用管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Authorize\AuthorizeLog'
        );
        $tree[] = $item;

        // 企业授权管理
        $item = array(
            'menu_name' => '企业授权管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 授权应用设置
        $item = array(
            'menu_name' => '授权应用设置',
            'menu_model' => 'qyweixin-snsapplication',
            'level' => '企业授权管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\SnsApplication'
        );
        $tree[] = $item;

        // 回调地址安全域名
        $item = array(
            'menu_name' => '回调地址安全域名',
            'menu_model' => 'qyweixin-callbackurls',
            'level' => '企业授权管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Callbackurls'
        );
        $tree[] = $item;

        // 授权执行时间跟踪统计
        $item = array(
            'menu_name' => '授权执行时间跟踪统计',
            'menu_model' => 'qyweixin-scripttracking',
            'level' => '企业授权管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ScriptTracking'
        );
        $tree[] = $item;

        // 企业平台用户管理
        $item = array(
            'menu_name' => '企业平台用户管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // // 企业用户
        // $item = array(
        //     'menu_name' => '企业用户',
        //     'menu_model' => 'qyweixin-user',
        //     'level' => '企业平台用户管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Qyweixin\Models\User\User'
        // );
        // $tree[] = $item;

        // // 用户标签
        // $item = array(
        //     'menu_name' => '用户标签',
        //     'menu_model' => 'qyweixin-usertag',
        //     'level' => '企业平台用户管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Qyweixin\Models\User\Tag'
        // );
        // $tree[] = $item;

        // // 用户和用户标签对应设置
        // $item = array(
        //     'menu_name' => '用户和用户标签对应设置',
        //     'menu_model' => 'qyweixin-usertousertag',
        //     'level' => '企业平台用户管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Qyweixin\Models\User\UserToUserTag'
        // );
        // $tree[] = $item;

        // // 黑名单
        // $item = array(
        //     'menu_name' => '黑名单',
        //     'menu_model' => 'qyweixin-blackuser',
        //     'level' => '企业平台用户管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Qyweixin\Models\User\BlackUser'
        // );
        // $tree[] = $item;

        // 企业消息管理
        $item = array(
            'menu_name' => '企业消息管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 消息与事件接收日志
        $item = array(
            'menu_name' => '消息与事件接收日志',
            'menu_model' => 'qyweixin-msglog',
            'level' => '企业消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Msg\Log'
        );
        $tree[] = $item;

        // 企业被动回复用户消息管理
        $item = array(
            'menu_name' => '企业被动回复用户消息管理',
            'menu_model' => '',
            'level' => '企业消息管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业应用消息管理
        $item = array(
            'menu_name' => '企业应用消息管理',
            'menu_model' => '',
            'level' => '企业消息管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业群聊会话消息管理
        $item = array(
            'menu_name' => '企业群聊会话消息管理',
            'menu_model' => '',
            'level' => '企业消息管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业互联企业消息管理
        $item = array(
            'menu_name' => '企业互联企业消息管理',
            'menu_model' => '',
            'level' => '企业消息管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 被动回复用户消息设定
        $item = array(
            'menu_name' => '被动回复用户消息设定',
            'menu_model' => 'qyweixin-replymsg',
            'level' => '企业被动回复用户消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ReplyMsg\ReplyMsg'
        );
        $tree[] = $item;

        // 被动回复用户消息图文设置
        $item = array(
            'menu_name' => '被动回复用户消息图文设置',
            'menu_model' => 'qyweixin-replymsgnews',
            'level' => '企业被动回复用户消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ReplyMsg\News'
        );
        $tree[] = $item;

        // 被动回复用户消息发送日志
        $item = array(
            'menu_name' => '被动回复用户消息发送日志',
            'menu_model' => 'qyweixin-replymsgsendlog',
            'level' => '企业被动回复用户消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ReplyMsg\SendLog'
        );
        $tree[] = $item;

        // 应用消息
        $item = array(
            'menu_name' => '应用消息',
            'menu_model' => 'qyweixin-agentmsg',
            'level' => '企业应用消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AgentMsg\AgentMsg'
        );
        $tree[] = $item;

        // 应用消息图文设置
        $item = array(
            'menu_name' => '应用消息图文设置',
            'menu_model' => 'qyweixin-agentmsgnews',
            'level' => '企业应用消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AgentMsg\News'
        );
        $tree[] = $item;

        // 应用消息发送日志
        $item = array(
            'menu_name' => '应用消息发送日志',
            'menu_model' => 'qyweixin-agentmsgsendlog',
            'level' => '企业应用消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AgentMsg\SendLog'
        );
        $tree[] = $item;

        // 群聊会话
        $item = array(
            'menu_name' => '群聊会话',
            'menu_model' => 'qyweixin-appchat',
            'level' => '企业群聊会话消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AppchatMsg\Appchat'
        );
        $tree[] = $item;

        // 群聊会话消息
        $item = array(
            'menu_name' => '群聊会话消息',
            'menu_model' => 'qyweixin-appchatmsg',
            'level' => '企业群聊会话消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AppchatMsg\AppchatMsg'
        );
        $tree[] = $item;

        // 群聊会话消息图文设置
        $item = array(
            'menu_name' => '群聊会话消息图文设置',
            'menu_model' => 'qyweixin-appchatmsgnews',
            'level' => '企业群聊会话消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AppchatMsg\News'
        );
        $tree[] = $item;

        // 群聊会话消息发送日志
        $item = array(
            'menu_name' => '群聊会话消息发送日志',
            'menu_model' => 'qyweixin-appchatmsgsendlog',
            'level' => '企业群聊会话消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AppchatMsg\SendLog'
        );
        $tree[] = $item;


        // 互联企业消息
        $item = array(
            'menu_name' => '互联企业消息',
            'menu_model' => 'qyweixin-linkedcorpmsg',
            'level' => '企业互联企业消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\LinkedcorpMsg'
        );
        $tree[] = $item;

        // 互联企业消息图文设置
        $item = array(
            'menu_name' => '互联企业消息图文设置',
            'menu_model' => 'qyweixin-linkedcorpmsgnews',
            'level' => '企业互联企业消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\News'
        );
        $tree[] = $item;

        // 互联企业消息发送日志
        $item = array(
            'menu_name' => '互联企业消息发送日志',
            'menu_model' => 'qyweixin-linkedcorpmsgsendlog',
            'level' => '企业互联企业消息管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\SendLog'
        );
        $tree[] = $item;

        // 企业自定义菜单管理
        $item = array(
            'menu_name' => '企业自定义菜单管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 自定义菜单设置
        $item = array(
            'menu_name' => '自定义菜单设置',
            'menu_model' => 'qyweixin-menu',
            'level' => '企业自定义菜单管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Menu\Menu'
        );
        $tree[] = $item;

        // 企业素材管理
        $item = array(
            'menu_name' => '企业素材管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业临时素材管理
        $item = array(
            'menu_name' => '企业临时素材管理',
            'menu_model' => '',
            'level' => '企业素材管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 临时素材设置
        $item = array(
            'menu_name' => '临时素材设置',
            'menu_model' => 'qyweixin-media',
            'level' => '企业临时素材管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Media\Media'
        );
        $tree[] = $item;

        // 企业系统配置管理
        $item = array(
            'menu_name' => '企业系统配置管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 素材类型
        $item = array(
            'menu_name' => '素材类型',
            'menu_model' => 'qyweixin-mediatype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Media\Type'
        );
        $tree[] = $item;

        // 消息类型
        $item = array(
            'menu_name' => '消息类型',
            'menu_model' => 'qyweixin-msgtype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Msg\Type'
        );
        $tree[] = $item;

        // 回复消息类型
        $item = array(
            'menu_name' => '回复消息类型',
            'menu_model' => 'qyweixin-replymsgtype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ReplyMsg\Type'
        );
        $tree[] = $item;

        // 应用消息类型
        $item = array(
            'menu_name' => '应用消息类型',
            'menu_model' => 'qyweixin-agentmsgtype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AgentMsg\Type'
        );
        $tree[] = $item;

        // 应用消息发送方式
        $item = array(
            'menu_name' => '应用消息发送方式',
            'menu_model' => 'qyweixin-agentmsgsendmethod',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AgentMsg\SendMethod'
        );
        $tree[] = $item;

        // 群聊会话消息类型
        $item = array(
            'menu_name' => '群聊会话消息类型',
            'menu_model' => 'qyweixin-appchatmsgtype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\AppchatMsg\Type'
        );
        $tree[] = $item;

        // 互联企业消息类型
        $item = array(
            'menu_name' => '互联企业消息类型',
            'menu_model' => 'qyweixin-linkedcorpmsgtype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Linkedcorp\Type'
        );
        $tree[] = $item;

        // 互联企业消息发送方式
        $item = array(
            'menu_name' => '互联企业消息发送方式',
            'menu_model' => 'qyweixin-linkedcorpmsgsendmethod',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\LinkedcorpMsg\SendMethod'
        );
        $tree[] = $item;

        // 自定义菜单类型
        $item = array(
            'menu_name' => '自定义菜单类型',
            'menu_model' => 'qyweixin-menutype',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Menu\Type'
        );
        $tree[] = $item;

        // 语言
        $item = array(
            'menu_name' => '语言',
            'menu_model' => 'qyweixin-language',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Language'
        );
        $tree[] = $item;

        // 事件分类
        $item = array(
            'menu_name' => '事件分类',
            'menu_model' => 'qyweixin-eventcategory',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Event\Category'
        );
        $tree[] = $item;

        // 事件
        $item = array(
            'menu_name' => '事件',
            'menu_model' => 'qyweixin-event',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Event\Event'
        );
        $tree[] = $item;

        // 企业服务管理
        $item = array(
            'menu_name' => '企业服务管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 服务设置
        $item = array(
            'menu_name' => '服务设置',
            'menu_model' => 'qyweixin-service',
            'level' => '企业服务管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Service'
        );
        $tree[] = $item;

        // 企业关键词回复管理
        $item = array(
            'menu_name' => '企业关键词回复管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 关键字设定
        $item = array(
            'menu_name' => '关键字设定',
            'menu_model' => 'qyweixin-keyword',
            'level' => '企业关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Keyword\Keyword'
        );
        $tree[] = $item;

        // 非关键词
        $item = array(
            'menu_name' => '非关键词',
            'menu_model' => 'qyweixin-word',
            'level' => '企业关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Keyword\Word'
        );
        $tree[] = $item;

        // 关键词和回复消息对应设定
        $item = array(
            'menu_name' => '关键词和回复消息对应设定',
            'menu_model' => 'qyweixin-keywordtoreplymsg',
            'level' => '企业关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Keyword\KeywordToReplyMsg'
        );
        $tree[] = $item;

        // 关键词和应用消息对应设定
        $item = array(
            'menu_name' => '关键词和应用消息对应设定',
            'menu_model' => 'qyweixin-keywordtoagentmsg',
            'level' => '企业关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Keyword\KeywordToAgentMsg'
        );
        $tree[] = $item;

        // 关键词和服务对应设定
        $item = array(
            'menu_name' => '关键词和服务对应设定',
            'menu_model' => 'qyweixin-keywordtoservice',
            'level' => '企业关键词回复管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Keyword\KeywordToService'
        );
        $tree[] = $item;

        // 企业数据统计管理
        $item = array(
            'menu_name' => '企业数据统计管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业消息推送管理
        $item = array(
            'menu_name' => '企业消息推送管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业平台外部联系人管理
        $item = array(
            'menu_name' => '企业平台外部联系人管理',
            'menu_model' => '',
            'level' => '企业微信管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业服务人员管理
        $item = array(
            'menu_name' => '企业服务人员管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 服务人员
        $item = array(
            'menu_name' => '服务人员',
            'menu_model' => 'qyweixin-externalcontactfollowuser',
            'level' => '企业服务人员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\FollowUser'
        );
        $tree[] = $item;

        // 客户联系「联系我」管理
        $item = array(
            'menu_name' => '客户联系「联系我」管理',
            'menu_model' => 'qyweixin-externalcontactcontactway',
            'level' => '企业服务人员管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ContactWay'
        );
        $tree[] = $item;

        // 企业客户管理
        $item = array(
            'menu_name' => '企业客户管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 客户
        $item = array(
            'menu_name' => '客户',
            'menu_model' => 'qyweixin-externalcontactexternaluser',
            'level' => '企业客户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ExternalUser'
        );
        $tree[] = $item;

        // 添加外部联系人的企业成员
        $item = array(
            'menu_name' => '添加外部联系人的企业成员',
            'menu_model' => 'qyweixin-externalcontactexternaluserfollowuser',
            'level' => '企业客户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ExternalUserFollowUser'
        );
        $tree[] = $item;


        // 修改客户备注信息
        $item = array(
            'menu_name' => '修改客户备注信息',
            'menu_model' => 'qyweixin-externalcontactexternaluserremark',
            'level' => '企业客户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ExternalUserRemark'
        );
        $tree[] = $item;

        // 企业标签管理
        $item = array(
            'menu_name' => '企业标签管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业标签组
        $item = array(
            'menu_name' => '企业标签组',
            'menu_model' => 'qyweixin-externalcontactcorptaggroup',
            'level' => '企业标签管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\CorpTagGroup'
        );
        $tree[] = $item;

        // 企业标签
        $item = array(
            'menu_name' => '企业标签',
            'menu_model' => 'qyweixin-externalcontactcorptag',
            'level' => '企业标签管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\CorpTag'
        );
        $tree[] = $item;

        // 编辑客户企业标签
        $item = array(
            'menu_name' => '编辑客户企业标签',
            'menu_model' => 'qyweixin-externalcontactcorptagmark',
            'level' => '企业标签管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\CorpTagMark'
        );
        $tree[] = $item;

        // 企业客户群管理
        $item = array(
            'menu_name' => '企业客户群管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 客户群
        $item = array(
            'menu_name' => '客户群',
            'menu_model' => 'qyweixin-externalcontactgroupchat',
            'level' => '企业客户群管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChat'
        );
        $tree[] = $item;

        // 客户群成员
        $item = array(
            'menu_name' => '客户群成员',
            'menu_model' => 'qyweixin-externalcontactgroupchatmember',
            'level' => '企业客户群管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChatMember'
        );
        $tree[] = $item;

        // 配置客户群进群方式
        $item = array(
            'menu_name' => '配置客户群进群方式',
            'menu_model' => 'qyweixin-externalcontactgroupchatjoinway',
            'level' => '企业客户群管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChatJoinWay'
        );
        $tree[] = $item;

        // 企业客户朋友圈管理
        $item = array(
            'menu_name' => '企业客户朋友圈管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 客户朋友圈
        $item = array(
            'menu_name' => '客户朋友圈',
            'menu_model' => 'qyweixin-externalcontactmoment',
            'level' => '企业客户朋友圈管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\Moment'
        );
        $tree[] = $item;

        // 企业发表内容到客户的朋友圈
        $item = array(
            'menu_name' => '企业发表内容到客户的朋友圈',
            'menu_model' => 'qyweixin-externalcontactmomenttask',
            'level' => '企业客户朋友圈管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MomentTask'
        );
        $tree[] = $item;

        // 客户朋友圈企业发表列表
        $item = array(
            'menu_name' => '客户朋友圈企业发表列表',
            'menu_model' => 'qyweixin-externalcontactmomenttaskuser',
            'level' => '企业客户朋友圈管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MomentTaskUser'
        );
        $tree[] = $item;

        // 客户朋友圈发表时选择的可见范围
        $item = array(
            'menu_name' => '客户朋友圈发表时选择的可见范围',
            'menu_model' => 'qyweixin-externalcontactmomentcustomer',
            'level' => '企业客户朋友圈管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MomentCustomer'
        );
        $tree[] = $item;

        // 客户朋友圈发表后的可见客户列表
        $item = array(
            'menu_name' => '客户朋友圈发表后的可见客户列表',
            'menu_model' => 'qyweixin-externalcontactmomentsendresult',
            'level' => '企业客户朋友圈管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MomentSendResult'
        );
        $tree[] = $item;

        // 客户朋友圈的互动数据
        $item = array(
            'menu_name' => '客户朋友圈的互动数据',
            'menu_model' => 'qyweixin-externalcontactmomentcomment',
            'level' => '企业客户朋友圈管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MomentComment'
        );
        $tree[] = $item;

        // 企业客户消息推送管理
        $item = array(
            'menu_name' => '企业客户消息推送管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业群发消息
        $item = array(
            'menu_name' => '企业群发消息',
            'menu_model' => 'qyweixin-externalcontactmsgtemplate',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MsgTemplate'
        );
        $tree[] = $item;

        // 企业群发消息发送日志
        $item = array(
            'menu_name' => '企业群发消息发送日志',
            'menu_model' => 'qyweixin-externalcontactmsgtemplatesendlog',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\MsgTemplateSendLog'
        );
        $tree[] = $item;

        // 企业群发记录
        $item = array(
            'menu_name' => '企业群发记录',
            'menu_model' => 'qyweixin-externalcontactgroupmsg',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupMsg'
        );
        $tree[] = $item;

        // 企业群发成员发送任务
        $item = array(
            'menu_name' => '企业群发成员发送任务',
            'menu_model' => 'qyweixin-externalcontactgroupmsgtask',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupMsgTask'
        );
        $tree[] = $item;

        // 企业群发成员执行结果
        $item = array(
            'menu_name' => '企业群发成员执行结果',
            'menu_model' => 'qyweixin-externalcontactgroupmsgsendresult',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupMsgSendResult'
        );
        $tree[] = $item;

        // 新客户欢迎语
        $item = array(
            'menu_name' => '新客户欢迎语',
            'menu_model' => 'qyweixin-externalcontactwelcomemsg',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\WelcomeMsg'
        );
        $tree[] = $item;

        // 群欢迎语素材管理
        $item = array(
            'menu_name' => '群欢迎语素材管理',
            'menu_model' => 'qyweixin-externalcontactgroupwelcometemplate',
            'level' => '企业客户消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupWelcomeTemplate'
        );
        $tree[] = $item;

        // 企业服务人员离职管理
        $item = array(
            'menu_name' => '企业服务人员离职管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 待分配的离职成员列表
        $item = array(
            'menu_name' => '待分配的离职成员列表',
            'menu_model' => 'qyweixin-externalcontactunassigned',
            'level' => '企业服务人员离职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\Unassigned'
        );
        $tree[] = $item;

        // // 离职成员的外部联系人再分配
        // $item = array(
        //     'menu_name' => '离职成员的外部联系人再分配',
        //     'menu_model' => 'qyweixin-externalcontacttransfer',
        //     'level' => '企业服务人员离职管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\Transfer'
        // );
        // $tree[] = $item;

        // // 离职成员的群再分配
        // $item = array(
        //     'menu_name' => '离职成员的群再分配',
        //     'menu_model' => 'qyweixin-externalcontactgroupchattransfer',
        //     'level' => '企业服务人员离职管理',
        //     'icon' => '',
        //     'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChatTransfer'
        // );
        // $tree[] = $item;

        // 分配离职成员的客户
        $item = array(
            'menu_name' => '分配离职成员的客户',
            'menu_model' => 'qyweixin-externalcontactresignedtransfercustomer',
            'level' => '企业服务人员离职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ResignedTransferCustomer'
        );
        $tree[] = $item;

        // 离职成员的客户分配情况
        $item = array(
            'menu_name' => '离职成员的客户分配情况',
            'menu_model' => 'qyweixin-externalcontactresignedtransferresult',
            'level' => '企业服务人员离职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ResignedTransferResult'
        );
        $tree[] = $item;

        // 分配离职成员的客户群
        $item = array(
            'menu_name' => '分配离职成员的客户群',
            'menu_model' => 'qyweixin-externalcontactresignedtransfergroupchat',
            'level' => '企业服务人员离职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ResignedTransferGroupchat'
        );
        $tree[] = $item;

        // 企业服务人员在职管理
        $item = array(
            'menu_name' => '企业服务人员在职管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 分配在职成员的客户
        $item = array(
            'menu_name' => '分配在职成员的客户',
            'menu_model' => 'qyweixin-externalcontactonjobtransfercustomer',
            'level' => '企业服务人员在职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\OnjobTransferCustomer'
        );
        $tree[] = $item;

        // 在职成员的客户分配情况
        $item = array(
            'menu_name' => '在职成员的客户分配情况',
            'menu_model' => 'qyweixin-externalcontactonjobtransferresult',
            'level' => '企业服务人员在职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\OnjobTransferResult'
        );
        $tree[] = $item;

        // 分配在职成员的客户群
        $item = array(
            'menu_name' => '分配在职成员的客户群',
            'menu_model' => 'qyweixin-externalcontactonjobtransfergroupchat',
            'level' => '企业服务人员在职管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\OnjobTransferGroupchat'
        );
        $tree[] = $item;

        // 企业客户统计管理
        $item = array(
            'menu_name' => '企业客户统计管理',
            'menu_model' => '',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 联系客户按用户统计数据
        $item = array(
            'menu_name' => '联系客户按用户统计数据',
            'menu_model' => 'qyweixin-externalcontactuserbehaviordatabyuserid',
            'level' => '企业客户统计管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\UserBehaviorDataByUserid'
        );
        $tree[] = $item;

        // 客户群按群主统计数据
        $item = array(
            'menu_name' => '客户群按群主统计数据',
            'menu_model' => 'qyweixin-externalcontactgroupchatstatisticbyuserid',
            'level' => '企业客户统计管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\GroupChatStatisticByUserid'
        );
        $tree[] = $item;

        // 客户来源
        $item = array(
            'menu_name' => '客户来源',
            'menu_model' => 'qyweixin-externalcontactaddway',
            'level' => '企业系统配置管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\Addway'
        );
        $tree[] = $item;

        // 企业平台通讯录管理
        $item = array(
            'menu_name' => '企业平台通讯录管理',
            'menu_model' => '',
            'level' => '企业平台用户管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 成员
        $item = array(
            'menu_name' => '成员',
            'menu_model' => 'qyweixin-user',
            'level' => '企业平台用户管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\User'
        );
        $tree[] = $item;

        // 部门
        $item = array(
            'menu_name' => '部门',
            'menu_model' => 'qyweixin-department',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\Department'
        );
        $tree[] = $item;

        // 部门成员
        $item = array(
            'menu_name' => '部门成员',
            'menu_model' => 'qyweixin-departmentuser',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\DepartmentUser'
        );
        $tree[] = $item;

        // 标签
        $item = array(
            'menu_name' => '标签',
            'menu_model' => 'qyweixin-tag',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\Tag'
        );
        $tree[] = $item;

        // 成员标签
        $item = array(
            'menu_name' => '成员标签',
            'menu_model' => 'qyweixin-taguser',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\TagUser'
        );
        $tree[] = $item;

        // 部门标签
        $item = array(
            'menu_name' => '部门标签',
            'menu_model' => 'qyweixin-tagparty',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\TagParty'
        );
        $tree[] = $item;

        // 批量邀请成员
        $item = array(
            'menu_name' => '批量邀请成员',
            'menu_model' => 'qyweixin-batchinvite',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\BatchInvite'
        );
        $tree[] = $item;

        // 异步批量任务
        $item = array(
            'menu_name' => '异步批量任务',
            'menu_model' => 'qyweixin-batchjob',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\BatchJob'
        );
        $tree[] = $item;

        // 加入企业二维码
        $item = array(
            'menu_name' => '加入企业二维码',
            'menu_model' => 'qyweixin-corpjoinqrcode',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\CorpJoinQrcode'
        );
        $tree[] = $item;

        // 企业活跃成员数
        $item = array(
            'menu_name' => '企业活跃成员数',
            'menu_model' => 'qyweixin-useractivestat',
            'level' => '企业平台通讯录管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Contact\UserActiveStat'
        );
        $tree[] = $item;

        // 企业会话内容存档
        $item = array(
            'menu_name' => '企业会话内容存档',
            'menu_model' => '',
            'level' => '企业消息管理',
            'icon' => '',
            'model' => ''
        );
        $tree[] = $item;

        // 企业会话内容存档密钥
        $item = array(
            'menu_name' => '企业会话内容存档密钥',
            'menu_model' => 'qyweixin-msgauditsn',
            'level' => '企业会话内容存档',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\MsgAudit\Sn'
        );
        $tree[] = $item;

        // 企业会话内容存档最大SEQ
        $item = array(
            'menu_name' => '企业会话内容存档最大SEQ',
            'menu_model' => 'qyweixin-msgauditmaxseq',
            'level' => '企业会话内容存档',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\MsgAudit\Maxseq'
        );
        $tree[] = $item;

        // 企业会话内容存档会话内容
        $item = array(
            'menu_name' => '企业会话内容存档会话内容',
            'menu_model' => 'qyweixin-msgauditchatdata',
            'level' => '企业会话内容存档',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\MsgAudit\Chatdata'
        );
        $tree[] = $item;

        // 企业推送任务
        $item = array(
            'menu_name' => '企业推送任务',
            'menu_model' => 'qyweixin-notificationtask',
            'level' => '企业消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Notification\Task'
        );
        $tree[] = $item;

        // 企业推送任务日志
        $item = array(
            'menu_name' => '企业推送任务日志',
            'menu_model' => 'qyweixin-notificationtasklog',
            'level' => '企业消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Notification\TaskLog'
        );
        $tree[] = $item;

        // 企业推送任务处理
        $item = array(
            'menu_name' => '企业推送任务处理',
            'menu_model' => 'qyweixin-notificationtaskprocess',
            'level' => '企业消息推送管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\Notification\TaskProcess'
        );
        $tree[] = $item;

        // 企业商品图册管理
        $item = array(
            'menu_name' => '企业商品图册管理',
            'menu_model' => 'qyweixin-externalcontactproductalbum',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\ProductAlbum'
        );
        $tree[] = $item;

        // 企业聊天敏感词管理
        $item = array(
            'menu_name' => '企业聊天敏感词管理',
            'menu_model' => 'qyweixin-externalcontactinterceptrule',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\ExternalContact\InterceptRule'
        );
        $tree[] = $item;

        // 附件资源设置
        $item = array(
            'menu_name' => '附件资源设置',
            'menu_model' => 'qyweixin-attachment',
            'level' => '企业平台外部联系人管理',
            'icon' => '',
            'model' => '\App\Backend\Submodules\Qyweixin\Models\attachment\Attachment'
        );
        $tree[] = $item;

        return $tree;
    }
}
