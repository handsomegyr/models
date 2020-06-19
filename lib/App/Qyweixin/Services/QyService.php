<?php

namespace App\Qyweixin\Services;

class QyService
{

    private $authorizer_appid = "";

    private $provider_appid = "";

    private $providerConfig = array();

    private $authorizerConfig = array();

    private $agentid = 0;

    /**
     * @var \Qyweixin\Client
     */
    private $objQyWeixin = null;

    /**
     * @var \Weixin\Component
     */
    private $objQyWeixinProvider = null;

    /**
     * @var \App\Qyweixin\Models\Provider\Provider
     */
    private $modelQyweixinProvider;

    /**
     * @var \App\Qyweixin\Models\Authorize\Authorizer
     */
    private $modelQyweixinAuthorizer;

    /**
     * @var \App\Qyweixin\Models\Agent\Agent
     */
    private $modelQyweixinAgent;

    public function __construct($authorizer_appid, $provider_appid, $agentid)
    {
        $this->authorizer_appid = $authorizer_appid;
        $this->provider_appid = $provider_appid;
        $this->agentid = $agentid;
        $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinAgent = new \App\Qyweixin\Models\Agent\Agent();
    }

    public function getAppConfig4Provider()
    {
        $this->getToken4Provider();
        return $this->providerConfig;
    }

    public function getQyweixinProvider()
    {
        $this->getToken4Provider();

        $this->objQyWeixinProvider = new \Qyweixin\Service();
        if (!empty($this->providerConfig['access_token'])) {
            $this->objQyWeixinProvider->setAccessToken($this->providerConfig['access_token']);
        }
        return $this->objQyWeixinProvider;
    }

    public function getAppConfig4Authorizer()
    {
        $this->getToken4Authorizer();
        return $this->authorizerConfig;
    }

    public function getQyWeixinObject()
    {
        if (empty($this->agentid)) {
            $this->getToken4Authorizer();
            $this->objQyWeixin = new \Qyweixin\Client($this->authorizerConfig['appid'], $this->authorizerConfig['appsecret']);
            if (!empty($this->authorizerConfig['access_token'])) {
                $this->objQyWeixin->setAccessToken($this->authorizerConfig['access_token']);
            }
        } else {
            $agentInfo = $this->modelQyweixinAgent->getTokenByAppid($this->provider_appid, $this->authorizer_appid, $this->agentid);
            $this->objQyWeixin = new \Qyweixin\Client($agentInfo['authorizer_appid'], $agentInfo['secret']);
            if (!empty($agentInfo['access_token'])) {
                $this->objQyWeixin->setAccessToken($agentInfo['access_token']);
            }
        }

        return $this->objQyWeixin;
    }

    protected function getToken4Provider()
    {
        if (empty($this->providerConfig)) {
            $this->providerConfig = $this->modelQyweixinProvider->getTokenByAppid($this->provider_appid);
            if (empty($this->providerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
            }
        }
    }

    protected function getToken4Authorizer()
    {
        if (empty($this->authorizerConfig)) {
            $this->authorizerConfig = $this->modelQyweixinAuthorizer->getInfoByAppid($this->provider_appid, $this->authorizer_appid);
            if (empty($this->authorizerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
        }
    }

    public function getAccessToken4Agent()
    {
        $agentInfo = $this->modelQyweixinAgent->getTokenByAppid($this->provider_appid, $this->authorizer_appid, $this->agentid, true);
        if (empty($agentInfo)) {
            throw new \Exception("对应的运用不存在");
        }
        return $agentInfo;
    }

    public function getAccessToken4Authorizer()
    {
        $modelAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $authorizerInfo = $modelAuthorizer->getTokenByAppid($this->provider_appid, $this->authorizer_appid);
        if (empty($authorizerInfo)) {
            throw new \Exception("对应的授权方不存在");
        }
        return $authorizerInfo;
    }

    public function getAuthorizerInfo()
    {
        $modelAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $authorizerInfo = $modelAuthorizer->getInfoByAppid($this->provider_appid, $this->authorizer_appid, true);
        if (empty($authorizerInfo)) {
            throw new \Exception("对应的授权方不存在");
        }
        $res = $this->getQyweixinProvider()->apiGetAuthorizerInfo($this->authorizer_appid);
        $modelAuthorizer->updateAuthorizerInfo($authorizerInfo['id'], $res, $authorizerInfo['memo']);
        return $res;
    }

    /**
     * 所有文件size必须大于5个字节
     * 图片（image）：2MB，支持JPG,PNG格式
     * 语音（voice） ：2MB，播放长度不超过60s，仅支持AMR格式
     * 视频（video） ：10MB，支持MP4格式
     * 普通文件（file）：20MB
     */
    public function uploadMedia($media_rec_id)
    {
        $modelMedia = new \App\Qyweixin\Models\Media\Media();
        $mediaInfo = $modelMedia->getInfoById($media_rec_id);
        if (empty($mediaInfo)) {
            throw new \Exception("临时素材记录ID:{$media_rec_id}所对应的临时素材不存在");
        }

        $filePath = $modelMedia->getPhysicalFilePath($mediaInfo['media']);
        $res = $this->uploadMediaByApi($filePath, $mediaInfo['type'], $mediaInfo['media_id'], $mediaInfo['media_time']);

        // 发生了改变就更新
        if ($res['media_id'] != $mediaInfo['media_id']) {
            $modelMedia->recordMediaId($media_rec_id, $res, time());
        }

        return $res;
    }

    // 检查媒体文件是否过期
    public function isMediaTimeExpired($media_id, $media_time)
    {
        // 媒体文件在微信后台保存时间为3天，即3天后media_id失效。
        $expire_seconds = 24 * 3600 * 2.7;

        // 如果已上传并且没有过期
        if (!empty($media_id) && (strtotime($media_time) + $expire_seconds) > time()) {
            // throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材是已上传并且没有过期");
            return false;
        } else {
            return true;
        }
    }

    /**
     * 所有文件size必须大于5个字节
     * 图片（image）：2MB，支持JPG,PNG格式
     * 语音（voice） ：2MB，播放长度不超过60s，仅支持AMR格式
     * 视频（video） ：10MB，支持MP4格式
     * 普通文件（file）：20MB
     */
    public function uploadMediaByApi($media_url, $type, $media_id, $media_time)
    {
        // 检查是否过期
        $isExpired = $this->isMediaTimeExpired($media_id, $media_time);

        // 如果没有过期
        if (!$isExpired) {
            // throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材是已上传并且没有过期");
            $res = array();
            $res['media_id'] = $media_id;
            return $res;
        }

        $res = $this->getQyWeixinObject()
            ->getMediaManager()
            ->upload($type, $media_url);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [type] => thumb
        // [created_at] => 1557741369
        return $res;
    }

    /**
     * 上传图片
     */
    public function uploadMediaImgByApi($media_url)
    {
        $res = $this->getQyWeixinObject()
            ->getMediaManager()
            ->uploadImg($media_url);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [url] => http://p.qpic.cn/pic_wework/1696231148/61675075f03f76a4c20d8a547a3418d646ec2e2de106ff9a/0
        return $res;
    }

    public function addMsgTemplate($msgTemplateInfo)
    {
        $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();
        $msgTemplate = new \Qyweixin\Model\ExternalContact\MsgTemplate();
        $msgTemplate->chat_type = $msgTemplateInfo['chat_type'];
        if (!empty($msgTemplateInfo['external_userid'])) {
            if (is_string($msgTemplateInfo['external_userid'])) {
                $msgTemplate->external_userid = \json_decode($msgTemplateInfo['external_userid'], true);
            } else {
                $msgTemplate->external_userid = $msgTemplateInfo['external_userid'];
            }
        }
        $msgTemplate->sender = $msgTemplateInfo['sender'];

        $text = new \Qyweixin\Model\ExternalContact\Conclusion\Text($msgTemplateInfo['text_content']);
        $msgTemplate->text = $text;

        if (!empty($msgTemplateInfo['image_media_id']) || !empty($msgTemplateInfo['image_pic_url'])) {
            $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image($msgTemplateInfo['image_media_id'], $msgTemplateInfo['image_pic_url']);
            $msgTemplate->image = $image;
        }

        if (!empty($msgTemplateInfo['link_url'])) {
            $link = new \Qyweixin\Model\ExternalContact\Conclusion\Link($msgTemplateInfo['link_title'], $msgTemplateInfo['link_picurl'], $msgTemplateInfo['link_desc'], $msgTemplateInfo['link_url']);
            $msgTemplate->link = $link;
        }

        if (!empty($msgTemplateInfo['miniprogram_appid'])) {
            $miniprogram = new \Qyweixin\Model\ExternalContact\Conclusion\Miniprogram($msgTemplateInfo['miniprogram_title'], $msgTemplateInfo['miniprogram_pic_media_id'], $msgTemplateInfo['miniprogram_appid'], $msgTemplateInfo['miniprogram_page']);
            $msgTemplate->miniprogram = $miniprogram;
        }


        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->addMsgTemplate($msgTemplate);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        // "fail_list":["wmqfasd1e1927831123109rBAAAA"],
        // "msgid":"msgGCAAAXtWyujaWJHDDGi0mAAAA"
        $modelMsgTemplate->recordMsgId($msgTemplateInfo['id'], $res, time());
        return $res;
    }

    public function getGroupMsgResult($msgTemplateInfo)
    {
        $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupMsgResult($msgTemplateInfo['msgid']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        // "check_status": 1,
        // "detail_list": [
        //     {
        //         "external_userid": "wmqfasd1e19278asdasAAAA",
        //         "chat_id":"wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
        //         "userid": "zhangsan",
        //         "status": 1,
        //         "send_time": 1552536375
        //     }
        // ]
        $modelMsgTemplate->recordGroupMsgResult($msgTemplateInfo['id'], $res, time());

        // 同步数据到结果表
        // 同步detail_list
        if (!empty($res['detail_list'])) {
            $modelGroupMsgResult = new \App\Qyweixin\Models\ExternalContact\GroupMsgResult();
            $modelGroupMsgResult->syncDetailList($msgTemplateInfo['msgid'], $this->authorizer_appid, $this->provider_appid, $res, time());
        }
        return $res;
    }

    public function addContactWay($contactWayInfo)
    {
        $modelContactWay = new \App\Qyweixin\Models\ExternalContact\ContactWay();
        $contactWay = new \Qyweixin\Model\ExternalContact\ContactWay($contactWayInfo['type'], $contactWayInfo['scene']);
        $contactWay->style = $contactWayInfo['style'];
        $contactWay->remark = $contactWayInfo['remark'];
        $contactWay->skip_verify = empty($contactWayInfo['skip_verify']) ? false : true;
        $contactWay->state = $contactWayInfo['state'];
        if (!empty($contactWayInfo['user'])) {
            if (is_string($contactWayInfo['user'])) {
                $contactWay->user = \json_decode($contactWayInfo['user'], true);
            } else {
                $contactWay->user = $contactWayInfo['user'];
            }
        }
        if (!empty($contactWayInfo['party'])) {
            if (is_string($contactWayInfo['party'])) {
                $contactWay->party = \json_decode($contactWayInfo['party'], true);
            } else {
                $contactWay->party = $contactWayInfo['party'];
            }
        }
        $contactWay->is_temp = empty($contactWayInfo['is_temp']) ? false : true;
        if (!empty($contactWay->is_temp)) {
            $contactWay->expires_in = $contactWayInfo['expires_in'];
            $contactWay->chat_expires_in = $contactWayInfo['chat_expires_in'];
            $contactWay->unionid = $contactWayInfo['unionid'];

            $conclusion = new \Qyweixin\Model\ExternalContact\Conclusion();
            $text = new \Qyweixin\Model\ExternalContact\Conclusion\Text($contactWayInfo['conclusions_text_content']);
            $conclusion->text = $text;

            if (!empty($contactWayInfo['conclusions_image_media_id']) || !empty($contactWayInfo['conclusions_image_pic_url'])) {
                $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image($contactWayInfo['conclusions_image_media_id'], $contactWayInfo['conclusions_image_pic_url']);
                $conclusion->image = $image;
            }

            if (!empty($contactWayInfo['conclusions_link_url'])) {
                $link = new \Qyweixin\Model\ExternalContact\Conclusion\Link($contactWayInfo['conclusions_link_title'], $contactWayInfo['conclusions_link_picurl'], $contactWayInfo['conclusions_link_desc'], $contactWayInfo['conclusions_link_url']);
                $conclusion->link = $link;
            }

            if (!empty($contactWayInfo['conclusions_miniprogram_appid'])) {
                $miniprogram = new \Qyweixin\Model\ExternalContact\Conclusion\Miniprogram($contactWayInfo['conclusions_miniprogram_title'], $contactWayInfo['conclusions_miniprogram_pic_media_id'], $contactWayInfo['conclusions_miniprogram_appid'], $contactWayInfo['conclusions_miniprogram_page']);
                $conclusion->miniprogram = $miniprogram;
            }
            $contactWay->conclusion = $conclusion;
        }

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->addContactWay($contactWay);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        // "config_id":"42b34949e138eb6e027c123cba77fAAA"
        $modelContactWay->recordConfigId($contactWayInfo['id'], $res, time());
        return $res;
    }

    public function createMenu()
    {
        $modelMenu = new \App\Qyweixin\Models\Menu\Menu();
        $menus = $modelMenu->buildMenu($this->authorizer_appid, $this->provider_appid, $this->agentid);
        // return $menus;
        $res = $this->getQyWeixinObject()
            ->getMenuManager()
            ->create($this->agentid, $menus);

        return $res;
    }

    public function getUserInfo($user_id)
    {
        $modelUser = new \App\Qyweixin\Models\User\User();
        $userInfo = $modelUser->getInfoById($user_id);
        if (empty($userInfo)) {
            throw new \Exception("用户记录ID:{$user_id}所对应的用户不存在");
        }
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->getUserInfo($userInfo['openid']);
        /**
         * {
         * "subscribe": 1,
         * "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",
         * "nickname": "Band",
         * "sex": 1,
         * "language": "zh_CN",
         * "city": "广州",
         * "province": "广东",
         * "country": "中国",
         * "headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
         * "subscribe_time": 1382694957,
         * "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
         * "remark": "",
         * "groupid": 0,
         * "tagid_list":[128,2],
         * "subscribe_scene": "ADD_SCENE_QR_CODE",
         * "qr_scene": 98765,
         * "qr_scene_str": ""
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUser->updateUserInfoById($userInfo, $res);
        return $res;
    }

    public function answerReplyMsgs($FromUserName, $ToUserName, $match)
    {
        $modelReplyMsg = new \App\Qyweixin\Models\ReplyMsg\ReplyMsg();
        $replyMsgs = $modelReplyMsg->getReplyMsgsByKeyword($match);
        if (empty($replyMsgs)) {
            return "";
        }

        $objQyWeixin = $this->getQyWeixinObject();
        // 设定来源和目标用户的openid
        $objQyWeixin->setFromAndTo($FromUserName, $ToUserName);

        switch ($match['reply_msg_type']) {
            case 'news':
                $articles = array();
                // 获取图文列表
                $isFirst = empty($articles) ? true : false;
                $modelReplyMsgNews = new \App\Qyweixin\Models\ReplyMsg\News();
                $articles1 = $modelReplyMsgNews->getArticlesByReplyMsgId($replyMsgs[0]['id'], 'news', $isFirst);
                $articles = array_merge($articles, $articles1);
                $replymsg = $objQyWeixin
                    ->getReplyManager()
                    ->replyGraphText($articles);
                break;
            case 'text':
                $replymsg = $objQyWeixin
                    ->getReplyManager()
                    ->replyText($replyMsgs[0]['description']);
                break;
            case 'voice':
                $media_id = $this->getMediaId4ReplyMsg('voice', $replyMsgs[0]);
                $replymsg = $objQyWeixin
                    ->getReplyManager()
                    ->replyVoice($media_id);
                break;
            case 'video':
                $media_id = $this->getMediaId4ReplyMsg('video', $replyMsgs[0]);
                $replymsg = $objQyWeixin
                    ->getReplyManager()
                    ->replyVideo($replyMsgs[0]['title'], $replyMsgs[0]['description'], $media_id);
                break;
            case 'image':
                $media_id = $this->getMediaId4ReplyMsg('image', $replyMsgs[0]);
                $replymsg = $objQyWeixin
                    ->getReplyManager()
                    ->replyImage($media_id);
                break;
        }

        // 记录日志
        $modelReplyMsgSendLog = new \App\Qyweixin\Models\ReplyMsg\SendLog();
        $modelReplyMsgSendLog->record($replyMsgs[0]['provider_appid'], $replyMsgs[0]['authorizer_appid'], $replyMsgs[0]['agentid'], $replyMsgs[0]['id'], $replyMsgs[0]['name'], $replyMsgs[0]['msg_type'], $replyMsgs[0]['media'], $replyMsgs[0]['media_id'], $replyMsgs[0]['thumb_media'], $replyMsgs[0]['thumb_media_id'], $replyMsgs[0]['title'], $replyMsgs[0]['description'], $replyMsgs[0]['music'], $replyMsgs[0]['hqmusic'], $replyMsgs[0]['kf_account'], $match['id'], $match['keyword'], $match['reply_msg_type'], $ToUserName, $FromUserName, $replymsg, time());

        return $replymsg;
    }

    public function answerAgentMsgs($FromUserName, $ToUserName, $match)
    {
        $modelAgentMsg = new \App\Qyweixin\Models\AgentMsg\AgentMsg();
        $agentMsgs = $modelAgentMsg->getAgentMsgsByKeyword($match);
        if (empty($agentMsgs)) {
            return false;
        }

        $sendRet = $this->sendAgentMsg($FromUserName, $ToUserName, $agentMsgs[0], $match);
        return $sendRet['is_ok'];
    }

    public function sendAgentMsg($FromUserName, $ToUserName, $agentMsgInfo, $match)
    {
        $objQyWeixin = $this->getQyWeixinObject();
        $agentmsg = array();
        $agentid = $agentMsgInfo['agentid'];
        try {
            switch ($match['agent_msg_type']) {

                case 'text':
                    $objMsg = new \Qyweixin\Model\Message\Text($agentid, $agentMsgInfo['description'], $FromUserName);
                    break;
                case 'voice':
                    $media_id = $this->getMediaId4AgentMsg('voice', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\Voice($agentid, $media_id, $FromUserName);
                    break;
                case 'video':
                    $media_id = $this->getMediaId4AgentMsg('video', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\Video($agentid, $media_id, $FromUserName);
                    $objMsg->title = $agentMsgInfo['title'];
                    $objMsg->description = $agentMsgInfo['description'];
                    break;
                case 'file':
                    $media_id = $this->getMediaId4AgentMsg('file', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\File($agentid, $media_id, $FromUserName);
                    break;
                case 'image':
                    $media_id = $this->getMediaId4AgentMsg('image', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\Image($agentid, $media_id, $FromUserName);
                    break;
                case 'textcard':
                    $objMsg = new \Qyweixin\Model\Message\TextCard($agentid, $agentMsgInfo['title'], $agentMsgInfo['description'], $agentMsgInfo['url'], $FromUserName);
                    $objMsg->btntxt = $agentMsgInfo['btntxt'];
                    break;
                case 'news':
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelAgentMsgNews = new \App\Qyweixin\Models\AgentMsg\News();
                    $articles1 = $modelAgentMsgNews->getArticlesByAgentMsgId($agentMsgInfo['id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $objMsg = new \Qyweixin\Model\Message\News($agentid, $articles, $FromUserName);
                    break;
                case 'mpnews':
                    // 获取图文列表
                    $isFirst = false;
                    $modelAgentMsgNews = new \App\Qyweixin\Models\AgentMsg\News();
                    $articles = $modelAgentMsgNews->getArticlesByAgentMsgId($agentMsgInfo['id'], 'mpnews', $isFirst);
                    if (empty($articles)) {
                        throw new \Exception("应用消息ID:{$agentMsgInfo['agent_msg_id']}所对应的图文不存在");
                    }
                    foreach ($articles as &$article) {
                        $res4ThumbMedia = $this->uploadMedia($article['thumb_media']);
                        $article['thumb_media_id'] = $res4ThumbMedia['media_id'];
                        unset($article['thumb_media']);
                    }
                    $objMsg = new \Qyweixin\Model\Message\Mpnews($agentid, $articles, $FromUserName);
                    break;
                case 'markdown':
                    $objMsg = new \Qyweixin\Model\Message\Markdown($agentid, $agentMsgInfo['description'], $FromUserName);
                    break;
                case 'miniprogram_notice':
                    $objMsg = new \Qyweixin\Model\Message\MiniprogramNotice($agentMsgInfo['appid'], $agentMsgInfo['title'], $FromUserName);
                    $objMsg->page = $agentMsgInfo['pagepath'];
                    $objMsg->description = $agentMsgInfo['description'];
                    $objMsg->emphasis_first_item = $agentMsgInfo['emphasis_first_item'];
                    $objMsg->content_item = $agentMsgInfo['content_item'];
                    break;
                case 'taskcard':
                    $objMsg = new \Qyweixin\Model\Message\TaskCard($agentid, $agentMsgInfo['title'], $agentMsgInfo['description'], $agentMsgInfo['task_id'], $agentMsgInfo['btn'], $FromUserName);
                    $objMsg->url = $agentMsgInfo['url'];
                    break;
            }
            $objMsg->touser = $FromUserName;
            $objMsg->safe = intval($agentMsgInfo['safe']);
            $objMsg->enable_id_trans = intval($agentMsgInfo['enable_id_trans']);
            $objMsg->enable_duplicate_check = intval($agentMsgInfo['enable_duplicate_check']);
            $objMsg->duplicate_check_interval = intval($agentMsgInfo['duplicate_check_interval']);

            $agentmsg = $objQyWeixin->getMessageManager()->send($objMsg);
            if (!empty($agentmsg['errcode'])) {
                throw new \Exception($agentmsg['errmsg'], $agentmsg['errcode']);
            }
        } catch (\Exception $e) {
            $agentmsg['errorcode'] = $e->getCode();
            $agentmsg['errormsg'] = $e->getMessage();
        }

        if (empty($agentmsg)) {
            $agentmsg = "";
        } else {
            $agentmsg = \json_encode($agentmsg);
        }
        // 记录日志
        $modelAgentMsgSendLog = new \App\Qyweixin\Models\AgentMsg\SendLog();
        $modelAgentMsgSendLog->record(
            $agentMsgInfo['provider_appid'],
            $agentMsgInfo['authorizer_appid'],
            $agentMsgInfo['agentid'],
            $agentMsgInfo['id'],
            $agentMsgInfo['name'],
            $agentMsgInfo['msg_type'],
            $agentMsgInfo['media'],
            $agentMsgInfo['media_id'],
            $agentMsgInfo['title'],
            $agentMsgInfo['description'],
            $agentMsgInfo['url'],
            $agentMsgInfo['btntxt'],
            $agentMsgInfo['appid'],
            $agentMsgInfo['pagepath'],
            $agentMsgInfo['emphasis_first_item'],
            $agentMsgInfo['content_item'],
            $agentMsgInfo['task_id'],
            $agentMsgInfo['btn'],
            $agentMsgInfo['safe'],
            $agentMsgInfo['enable_id_trans'],
            $agentMsgInfo['enable_duplicate_check'],
            $agentMsgInfo['duplicate_check_interval'],
            $match['id'],
            $match['keyword'],
            $match['agent_msg_type'],
            $ToUserName,
            $FromUserName,
            $agentmsg,
            time()
        );

        return array(
            'is_ok' => true,
            'api_ret' => $agentmsg
        );
    }

    public function sendAppchatMsg($FromUserName, $ToUserName, $appchatMsgInfo, $match)
    {
        $objQyWeixin = $this->getQyWeixinObject();
        $appchatmsg = array();
        $agentid = $appchatMsgInfo['agentid'];
        $chatid = $appchatMsgInfo['chatid'];
        try {
            switch ($match['appchat_msg_type']) {
                case 'text':
                    $objMsg = new \Qyweixin\Model\AppchatMsg\Text($chatid, $appchatMsgInfo['description']);
                    break;
                case 'voice':
                    $media_id = $this->getMediaId4AppchatMsg('voice', $appchatMsgInfo);
                    $objMsg = new \Qyweixin\Model\AppchatMsg\Voice($chatid, $media_id);
                    break;
                case 'video':
                    $media_id = $this->getMediaId4AppchatMsg('video', $appchatMsgInfo);
                    $objMsg = new \Qyweixin\Model\AppchatMsg\Video($chatid, $media_id);
                    $objMsg->title = $appchatMsgInfo['title'];
                    $objMsg->description = $appchatMsgInfo['description'];
                    break;
                case 'file':
                    $media_id = $this->getMediaId4AppchatMsg('file', $appchatMsgInfo);
                    $objMsg = new \Qyweixin\Model\AppchatMsg\File($chatid, $media_id);
                    break;
                case 'image':
                    $media_id = $this->getMediaId4AppchatMsg('image', $appchatMsgInfo);
                    $objMsg = new \Qyweixin\Model\AppchatMsg\Image($chatid, $media_id);
                    break;
                case 'textcard':
                    $objMsg = new \Qyweixin\Model\AppchatMsg\TextCard($chatid, $appchatMsgInfo['title'], $appchatMsgInfo['description'], $appchatMsgInfo['url']);
                    $objMsg->btntxt = $appchatMsgInfo['btntxt'];
                    break;
                case 'news':
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelAppchatMsgNews = new \App\Qyweixin\Models\AppchatMsg\News();
                    $articles1 = $modelAppchatMsgNews->getArticlesByAppchatMsgId($appchatMsgInfo['id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $objMsg = new \Qyweixin\Model\AppchatMsg\News($chatid, $articles);
                    break;
                case 'mpnews':
                    // 获取图文列表
                    $isFirst = false;
                    $modelAppchatMsgNews = new \App\Qyweixin\Models\AppchatMsg\News();
                    $articles = $modelAppchatMsgNews->getArticlesByAppchatMsgId($appchatMsgInfo['id'], 'mpnews', $isFirst);
                    if (empty($articles)) {
                        throw new \Exception("群聊会话消息ID:{$appchatMsgInfo['appchat_msg_id']}所对应的图文不存在");
                    }
                    foreach ($articles as &$article) {
                        $res4ThumbMedia = $this->uploadMedia($article['thumb_media']);
                        $article['thumb_media_id'] = $res4ThumbMedia['media_id'];
                        unset($article['thumb_media']);
                    }
                    $objMsg = new \Qyweixin\Model\AppchatMsg\Mpnews($chatid, $articles);
                    break;
                case 'markdown':
                    $objMsg = new \Qyweixin\Model\AppchatMsg\Markdown($chatid, $appchatMsgInfo['description']);
                    break;
            }

            $objMsg->safe = intval($appchatMsgInfo['safe']);
            $appchatmsg = $objQyWeixin->getAppchatManager()->send($objMsg);
            if (!empty($appchatmsg['errcode'])) {
                throw new \Exception($appchatmsg['errmsg'], $appchatmsg['errcode']);
            }
        } catch (\Exception $e) {
            $appchatmsg['errorcode'] = $e->getCode();
            $appchatmsg['errormsg'] = $e->getMessage();
        }

        if (empty($appchatmsg)) {
            $appchatmsg = "";
        } else {
            $appchatmsg = \json_encode($appchatmsg);
        }
        // 记录日志
        $modelAppchatMsgSendLog = new \App\Qyweixin\Models\AppchatMsg\SendLog();
        $modelAppchatMsgSendLog->record(
            $appchatMsgInfo['provider_appid'],
            $appchatMsgInfo['authorizer_appid'],
            $appchatMsgInfo['agentid'],
            $appchatMsgInfo['chatid'],
            $appchatMsgInfo['id'],
            $appchatMsgInfo['name'],
            $appchatMsgInfo['msg_type'],
            $appchatMsgInfo['media'],
            $appchatMsgInfo['media_id'],
            $appchatMsgInfo['title'],
            $appchatMsgInfo['description'],
            $appchatMsgInfo['url'],
            $appchatMsgInfo['btntxt'],
            $appchatMsgInfo['safe'],
            $match['id'],
            $match['keyword'],
            $match['appchat_msg_type'],
            $ToUserName,
            $FromUserName,
            $appchatmsg,
            time()
        );

        return array(
            'is_ok' => true,
            'api_ret' => $appchatmsg
        );
    }

    public function sendLinkedcorpMsg($FromUserName, $ToUserName, $linkedcorpMsgInfo, $match)
    {
        $objQyWeixin = $this->getQyWeixinObject();
        $linkedcorpmsg = array();
        $agentid = $linkedcorpMsgInfo['agentid'];
        try {
            switch ($match['linkedcorp_msg_type']) {
                case 'text':
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\Text($agentid, $linkedcorpMsgInfo['description']);
                    break;
                case 'voice':
                    $media_id = $this->getMediaId4LinkedcorpMsg('voice', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\Voice($agentid, $media_id);
                    break;
                case 'video':
                    $media_id = $this->getMediaId4LinkedcorpMsg('video', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\Video($agentid, $media_id);
                    $objMsg->title = $linkedcorpMsgInfo['title'];
                    $objMsg->description = $linkedcorpMsgInfo['description'];
                    break;
                case 'file':
                    $media_id = $this->getMediaId4LinkedcorpMsg('file', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\File($agentid, $media_id);
                    break;
                case 'image':
                    $media_id = $this->getMediaId4LinkedcorpMsg('image', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\Image($agentid, $media_id);
                    break;
                case 'textcard':
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\TextCard($agentid, $linkedcorpMsgInfo['title'], $linkedcorpMsgInfo['description'], $linkedcorpMsgInfo['url']);
                    $objMsg->btntxt = $linkedcorpMsgInfo['btntxt'];
                    break;
                case 'news':
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelLinkedcorpMsgNews = new \App\Qyweixin\Models\LinkedcorpMsg\News();
                    $articles1 = $modelLinkedcorpMsgNews->getArticlesByLinkedcorpMsgId($linkedcorpMsgInfo['id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\News($agentid, $articles);
                    break;
                case 'mpnews':
                    // 获取图文列表
                    $isFirst = false;
                    $modelLinkedcorpMsgNews = new \App\Qyweixin\Models\LinkedcorpMsg\News();
                    $articles = $modelLinkedcorpMsgNews->getArticlesByLinkedcorpMsgId($linkedcorpMsgInfo['id'], 'mpnews', $isFirst);
                    if (empty($articles)) {
                        throw new \Exception("互联企业消息ID:{$linkedcorpMsgInfo['linkedcorp_msg_id']}所对应的图文不存在");
                    }
                    foreach ($articles as &$article) {
                        $res4ThumbMedia = $this->uploadMedia($article['thumb_media']);
                        $article['thumb_media_id'] = $res4ThumbMedia['media_id'];
                        unset($article['thumb_media']);
                    }
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\Mpnews($agentid, $articles);
                    break;
                case 'markdown':
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\Markdown($agentid, $linkedcorpMsgInfo['description']);
                    break;
                case 'miniprogram_notice':
                    $objMsg = new \Qyweixin\Model\LinkedcorpMsg\MiniprogramNotice($linkedcorpMsgInfo['appid'], $linkedcorpMsgInfo['title']);
                    $objMsg->page = $linkedcorpMsgInfo['pagepath'];
                    $objMsg->description = $linkedcorpMsgInfo['description'];
                    $objMsg->emphasis_first_item = $linkedcorpMsgInfo['emphasis_first_item'];
                    $objMsg->content_item = $linkedcorpMsgInfo['content_item'];
                    break;
            }

            $objMsg->touser = array($FromUserName);
            $objMsg->toall = intval($linkedcorpMsgInfo['toall']);
            $objMsg->safe = intval($linkedcorpMsgInfo['safe']);
            $linkedcorpmsg = $objQyWeixin->getLinkedcorpMessageManager()->send($objMsg);
            if (!empty($linkedcorpmsg['errcode'])) {
                throw new \Exception($linkedcorpmsg['errmsg'], $linkedcorpmsg['errcode']);
            }
        } catch (\Exception $e) {
            $linkedcorpmsg['errorcode'] = $e->getCode();
            $linkedcorpmsg['errormsg'] = $e->getMessage();
        }

        if (empty($linkedcorpmsg)) {
            $linkedcorpmsg = "";
        } else {
            $linkedcorpmsg = \json_encode($linkedcorpmsg);
        }
        // 记录日志
        $modelLinkedcorpMsgSendLog = new \App\Qyweixin\Models\LinkedcorpMsg\SendLog();
        $modelLinkedcorpMsgSendLog->record(
            $linkedcorpMsgInfo['provider_appid'],
            $linkedcorpMsgInfo['authorizer_appid'],
            $linkedcorpMsgInfo['agentid'],
            $linkedcorpMsgInfo['id'],
            $linkedcorpMsgInfo['name'],
            $linkedcorpMsgInfo['msg_type'],
            $linkedcorpMsgInfo['media'],
            $linkedcorpMsgInfo['media_id'],
            $linkedcorpMsgInfo['title'],
            $linkedcorpMsgInfo['description'],
            $linkedcorpMsgInfo['url'],
            $linkedcorpMsgInfo['btntxt'],
            $linkedcorpMsgInfo['appid'],
            $linkedcorpMsgInfo['pagepath'],
            $linkedcorpMsgInfo['emphasis_first_item'],
            $linkedcorpMsgInfo['content_item'],
            $linkedcorpMsgInfo['toall'],
            $linkedcorpMsgInfo['safe'],
            $match['id'],
            $match['keyword'],
            $match['linkedcorp_msg_type'],
            $ToUserName,
            $FromUserName,
            $linkedcorpmsg,
            time()
        );

        return array(
            'is_ok' => true,
            'api_ret' => $linkedcorpmsg
        );
    }

    //获取配置了客户联系功能的成员列表
    public function getFollowUserList()
    {
        $modelFollowUser = new \App\Qyweixin\Models\ExternalContact\FollowUser();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getFollowUserList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "follow_user":[
         *     "zhangsan",
         *     "lissi"
         *  ]
         * }
         */
        $modelFollowUser->syncFollowUserList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    //获取客户列表
    public function getExternalUserList($userid)
    {
        $modelExternalUser = new \App\Qyweixin\Models\ExternalContact\ExternalUser();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->list($userid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "external_userid":
         * [
         *    "woAJ2GCAAAXtWyujaWJHDDGi0mACAAA",
         *    "wmqfasd1e1927831291723123109rAAA"
         * ]
         * }
         */
        $modelExternalUser->syncExternalUserList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    //获取客户详情
    public function getExternalUserInfo($external_userid)
    {
        $modelExternalUser = new \App\Qyweixin\Models\ExternalContact\ExternalUser();
        $userInfo = $modelExternalUser->getInfoByExternalUserId($external_userid, $this->authorizer_appid);
        if (empty($userInfo)) {
            throw new \Exception("客户ID:{$external_userid}所对应的记录不存在");
        }

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->get($external_userid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "external_contact":{
         * "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA",
         *      "name":"李四",        
         *      "position":"Manager",
       
         * }
         */
        $modelExternalUser->updateExternalUserInfoByApi($userInfo, $res, time());

        // 同步follow_user
        if (!empty($res['follow_user'])) {
            $modelExternalUserFollowUser = new \App\Qyweixin\Models\ExternalContact\ExternalUserFollowUser();
            $modelExternalUserFollowUser->syncFollowUserList($external_userid, $this->authorizer_appid, $this->provider_appid, $res, time());
        }

        return $res;
    }

    //获取企业标签库
    public function getCorpTagList($tag_id)
    {
        $modelCorpTag = new \App\Qyweixin\Models\ExternalContact\CorpTag();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getCorpTagList($tag_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0, 
         * "errmsg": "ok", 
         * "tag_group": [
         *         {
         *             "group_id": "etliEKBwAAYn6YcE9PX3sMed6g_AEoAg", 
         *             "group_name": "客户等级", 
         *             "create_time": 1565579198, 
         *             "tag": [
         *                 {
         *                     "id": "etliEKBwAAlrIZlfCiMY5y6720aAZvgA", 
         *                     "name": "一般", 
         *                     "create_time": 1565579198, 
         *                     "order": 0
         *                 }, 
         *                 {
         *                     "id": "etliEKBwAAyT_-bkMOvWrbJOiKTZHDlg", 
         *                     "name": "重要", 
         *                     "create_time": 1565579198, 
         *                     "order": 0
         *                 }, 
         *                {
         *                     "id": "etliEKBwAAZUJLepM6EZk3DFw9E6zpkA", 
         *                     "name": "核心", 
         *                     "create_time": 1565579198, 
         *                     "order": 0
         *                 }
         *             ], 
         *             "order": 0
         *         }
         *    ]
         * }
         */
        $modelCorpTag->syncCorpTagList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    private function getMediaId4ReplyMsg($type, $reply)
    {
        if (!empty($reply['media'])) {
            $media_result = $this->uploadMedia($reply['media']);
            return $media_result['media_id'];
        } else {
            throw new \Exception("未指定临时素材", 99999);
        }
    }

    private function getMediaId4AgentMsg($type, $agentMsg)
    {
        if (!empty($agentMsg['media'])) {
            $media_result = $this->uploadMedia($agentMsg['media']);
            return $media_result['media_id'];
        } else {
            throw new \Exception("未指定临时素材", 99999);
        }
    }

    private function getMediaId4AppchatMsg($type, $appchatMsg)
    {
        if (!empty($appchatMsg['media'])) {
            $media_result = $this->uploadMedia($appchatMsg['media']);
            return $media_result['media_id'];
        } else {
            throw new \Exception("未指定临时素材", 99999);
        }
    }

    private function getMediaId4LinkedcorpMsg($type, $linkedcorpMsg)
    {
        if (!empty($linkedcorpMsg['media'])) {
            $media_result = $this->uploadMedia($linkedcorpMsg['media']);
            return $media_result['media_id'];
        } else {
            throw new \Exception("未指定临时素材", 99999);
        }
    }

    private function replaceDescription($desc)
    {
        if (empty($desc) || !is_string($desc)) {
            return '';
        }
        $pattern = '/<img(.*)src(.*)=(.*)"(.*)"/U';
        preg_replace_callback($pattern, function ($matches) use (&$desc) {
            if (isset($matches[4])) {
                $img_tag_src = array_pop($matches);
                // 如果不是微信服务器上的图片
                if (strpos($img_tag_src, 'http://mmbiz.qpic.cn/') === false) {
                    $img_url = $this->getQyWeixinObject()->getMediaManager()->uploadImg($img_tag_src);
                    // $this->_opt_log->write(__METHOD__, $img_url, '上传素材');
                    if (isset($img_url['errcode'])) {
                        throw new \Exception($img_url['errmsg'], $img_url['errcode']);
                    }
                    if (empty($img_url['url'])) {
                        // var_dump($img_url);
                        // $this->_opt_log->write(__METHOD__, $img_url, '上上传图片到微信服务器失败');
                        throw new \Exception('上传图片到微信服务器失败', -101);
                    }
                    $img_url['url'] = str_replace('\/', '/', $img_url['url']);
                    $desc = str_replace($img_tag_src, $img_url['url'], $desc);
                }
            }
        }, $desc);

        return $desc;
    }
}
