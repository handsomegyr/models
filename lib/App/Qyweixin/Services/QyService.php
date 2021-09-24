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
     *
     * @var \Qyweixin\Service
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

    public function getAuthorizerAppid()
    {
        return $this->authorizer_appid;
    }

    public function getProviderAppid()
    {
        return $this->provider_appid;
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
            $this->authorizerConfig = $this->modelQyweixinAuthorizer->getInfoByAppid($this->provider_appid, $this->authorizer_appid, true);
            if (empty($this->authorizerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
        }
    }

    public function getAccessToken4Agent()
    {
        $agentInfo = $this->modelQyweixinAgent->getTokenByAppid($this->provider_appid, $this->authorizer_appid, $this->agentid);
        if (empty($agentInfo)) {
            throw new \Exception("对应的运用不存在");
        }
        return $agentInfo;
    }

    public function getAgentInfo()
    {
        $agentInfo = $this->getAccessToken4Agent();

        $res = $this->getQyWeixinObject()
            ->getAgentManager()
            ->get($this->agentid);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "agentid": 1000005,
         * "name": "HR助手",
         * "square_logo_url": "https://p.qlogo.cn/bizmail/FicwmI50icF8GH9ib7rUAYR5kicLTgP265naVFQKnleqSlRhiaBx7QA9u7Q/0",
         * "description": "HR服务与员工自助平台",
         * "allow_userinfos": {
         * "user": [
         * {"userid": "zhangshan"},
         * {"userid": "lisi"}
         * ]
         * },
         * "allow_partys": {
         * "partyid": [1]
         * },
         * "allow_tags": {
         * "tagid": [1,2,3]
         * },
         * "close": 0,
         * "redirect_domain": "open.work.weixin.qq.com",
         * "report_location_flag": 0,
         * "isreportenter": 0,
         * "home_url": "https://open.work.weixin.qq.com"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $this->modelQyweixinAgent->updateAgentInfo($agentInfo['_id'], $res, time(), $agentInfo['memo']);
        return $res;
    }

    public function getAgentList()
    {
        $res = $this->getQyWeixinObject()
            ->getAgentManager()
            ->getAgentList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        return $res;
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

    //获取应用的jsapi_ticket
    public function getJsapiTicket4Agent()
    {
        $agentInfo = $this->getAccessToken4Agent();
        return $agentInfo['jsapi_ticket'];
    }

    //获取应用的JS-SDK使用权限签名
    public function getSignPackage($url)
    {
        $jsapi_ticket = $this->getJsapiTicket4Agent();
        $objJssdk = new \Qyweixin\Jssdk();
        return $objJssdk->getSignPackage($url, $jsapi_ticket);
    }

    /**
     * 所有文件size必须大于5个字节
     * 图片（image）：2MB，支持JPG,PNG格式
     * 语音（voice） ：2MB，播放长度不超过60s，仅支持AMR格式
     * 视频（video） ：10MB，支持MP4格式
     * 普通文件（file）：20MB
     */
    public function uploadMedia($mediaInfo)
    {
        $modelMedia = new \App\Qyweixin\Models\Media\Media();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($mediaInfo)) {
            $media_rec_id = $mediaInfo;
            $mediaInfo = $modelMedia->getInfoById($media_rec_id);
            if (empty($mediaInfo)) {
                throw new \Exception("临时素材记录ID:{$media_rec_id}所对应的临时素材不存在");
            }
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

    // 添加企业群发消息任务
    public function addMsgTemplate($FromUserName, $ToUserName, $msgTemplateInfo, $match)
    {
        $is_ok = false;
        $msg_template_content = array();
        $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();
        $chat_type = $msgTemplateInfo['chat_type'];

        try {
            //默认为single，表示发送给客户，group表示发送给客户群'
            if ($chat_type == 'single') {
                $msgTemplateInfo['external_userid'] = $ToUserName;
                $msgTemplateInfo['sender'] = "";
            } elseif ($chat_type == 'group') {
                $msgTemplateInfo['external_userid'] = '';
                $msgTemplateInfo['sender'] = $ToUserName;
            }

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

            if (!empty($msgTemplateInfo['image_media'])) {
                if (!empty($msgTemplateInfo['image_pic_url'])) {
                    $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image("", $msgTemplateInfo['image_pic_url']);
                } else {
                    $res = $this->uploadMediaByApi($msgTemplateInfo['image_media'], "image", $msgTemplateInfo['image_media_id'], $msgTemplateInfo['image_media_created_at']);
                    // 发生了改变就更新
                    if ($res['media_id'] != $msgTemplateInfo['image_media_id']) {
                        $modelMsgTemplate->recordMediaId($msgTemplateInfo['_id'], $res, time());
                        $msgTemplateInfo['image_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
                    }
                    $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image($res['media_id'], "");
                }
                // $msgTemplate->image = $image;
                $msgTemplate->attachments[] = $image;
            }

            if (!empty($msgTemplateInfo['link_url'])) {
                $link = new \Qyweixin\Model\ExternalContact\Conclusion\Link($msgTemplateInfo['link_title'], $msgTemplateInfo['link_picurl'], $msgTemplateInfo['link_desc'], $msgTemplateInfo['link_url']);
                // $msgTemplate->link = $link;                
                $msgTemplate->attachments[] = $link;
            }

            if (!empty($msgTemplateInfo['miniprogram_appid'])) {

                if (!empty($msgTemplateInfo['miniprogram_pic_media'])) {
                    $res2 = $this->uploadMediaByApi($msgTemplateInfo['miniprogram_pic_media'], "image", $msgTemplateInfo['miniprogram_pic_media_id'], $msgTemplateInfo['miniprogram_pic_media_created_at']);
                    // 发生了改变就更新
                    if ($res2['media_id'] != $msgTemplateInfo['miniprogram_pic_media_id']) {
                        $modelMsgTemplate->recordMediaId4Miniprogram($msgTemplateInfo['_id'], $res2, time());
                        $msgTemplateInfo['miniprogram_pic_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res2['created_at']);
                    }
                }
                $miniprogram = new \Qyweixin\Model\ExternalContact\Conclusion\Miniprogram($msgTemplateInfo['miniprogram_title'], $res2['media_id'], $msgTemplateInfo['miniprogram_appid'], $msgTemplateInfo['miniprogram_page']);
                // $msgTemplate->miniprogram = $miniprogram;
                $msgTemplate->attachments[] = $miniprogram;
            }

            if (!empty($msgTemplateInfo['video_media'])) {
                $res = $this->uploadMediaByApi($msgTemplateInfo['video_media'], "video", $msgTemplateInfo['video_media_id'], $msgTemplateInfo['video_media_created_at']);
                // 发生了改变就更新
                if ($res['media_id'] != $msgTemplateInfo['video_media_id']) {
                    $modelMsgTemplate->recordMediaId4Video($msgTemplateInfo['_id'], $res, time());
                    $msgTemplateInfo['video_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
                }
                $video = new \Qyweixin\Model\ExternalContact\Conclusion\Video($res['media_id']);
                $msgTemplate->attachments[] = $video;
            }

            $msg_template_content = $this->getQyWeixinObject()
                ->getExternalContactManager()
                ->addMsgTemplate($msgTemplate);
            if (!empty($msg_template_content['errcode'])) {
                throw new \Exception($msg_template_content['errmsg'], $msg_template_content['errcode']);
            }
            $is_ok = true;

            // "fail_list":["wmqfasd1e1927831123109rBAAAA"],
            // "msgid":"msgGCAAAXtWyujaWJHDDGi0mAAAA"
            // $modelMsgTemplate->recordMsgId($msgTemplateInfo['_id'], $res, time());
        } catch (\Exception $e) {
            $msg_template_content['errorcode'] = $e->getCode();
            $msg_template_content['errormsg'] = $e->getMessage();
        }

        if (empty($msg_template_content)) {
            $msg_template_content = array();
        }
        // 记录日志
        $modelMsgTemplateSendLog = new \App\Qyweixin\Models\ExternalContact\MsgTemplateSendLog();
        $modelMsgTemplateSendLog->record(
            $msgTemplateInfo['provider_appid'],
            $msgTemplateInfo['authorizer_appid'],
            $msgTemplateInfo['agentid'],
            $msgTemplateInfo['id'],
            $msgTemplateInfo['name'],
            $msgTemplateInfo['chat_type'],
            $msgTemplateInfo['external_userid'],
            $msgTemplateInfo['sender'],
            $msgTemplateInfo['text_content'],
            $msgTemplateInfo['image_media'],
            $msgTemplateInfo['image_media_id'],
            $msgTemplateInfo['image_pic_url'],
            $msgTemplateInfo['image_media_created_at'],
            $msgTemplateInfo['link_title'],
            $msgTemplateInfo['link_picurl'],
            $msgTemplateInfo['link_desc'],
            $msgTemplateInfo['link_url'],
            $msgTemplateInfo['miniprogram_title'],
            $msgTemplateInfo['miniprogram_pic_media'],
            $msgTemplateInfo['miniprogram_pic_media_id'],
            $msgTemplateInfo['miniprogram_pic_media_created_at'],
            $msgTemplateInfo['miniprogram_appid'],
            $msgTemplateInfo['miniprogram_page'],
            $msgTemplateInfo['video_media'],
            $msgTemplateInfo['video_media_id'],
            $msgTemplateInfo['video_media_created_at'],
            $match['id'],
            $match['keyword'],
            $match['msg_template_chat_type'],
            $ToUserName,
            $FromUserName,
            \\App\Common\Utils\Helper::myJsonEncode($msg_template_content),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $msg_template_content
        );
    }

    // 配置客户联系「联系我」方式
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

            if (!empty($contactWayInfo['conclusions_image_media'])) {
                if (!empty($contactWayInfo['conclusions_image_pic_url'])) {
                    $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image("", $contactWayInfo['conclusions_image_pic_url']);
                } else {
                    $res = $this->uploadMediaByApi($contactWayInfo['conclusions_image_media'], "image", $contactWayInfo['conclusions_image_media_id'], $contactWayInfo['conclusions_image_media_created_at']);
                    // 发生了改变就更新
                    if ($res['media_id'] != $contactWayInfo['conclusions_image_media_id']) {
                        $modelContactWay->recordMediaId($contactWayInfo['_id'], $res, time());
                    }
                    $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image($res['media_id'], "");
                }
                $conclusion->image = $image;
            }

            if (!empty($contactWayInfo['conclusions_link_url'])) {
                $link = new \Qyweixin\Model\ExternalContact\Conclusion\Link($contactWayInfo['conclusions_link_title'], $contactWayInfo['conclusions_link_picurl'], $contactWayInfo['conclusions_link_desc'], $contactWayInfo['conclusions_link_url']);
                $conclusion->link = $link;
            }

            if (!empty($contactWayInfo['conclusions_miniprogram_appid'])) {
                if (!empty($contactWayInfo['conclusions_miniprogram_pic_media'])) {
                    $res2 = $this->uploadMediaByApi($contactWayInfo['conclusions_miniprogram_pic_media'], "image", $contactWayInfo['conclusions_miniprogram_pic_media_id'], $contactWayInfo['conclusions_miniprogram_pic_media_created_at']);
                    // 发生了改变就更新
                    if ($res2['media_id'] != $contactWayInfo['conclusions_miniprogram_pic_media_id']) {
                        $modelContactWay->recordMediaId4Miniprogram($contactWayInfo['_id'], $res2, time());
                    }
                }
                $miniprogram = new \Qyweixin\Model\ExternalContact\Conclusion\Miniprogram($contactWayInfo['conclusions_miniprogram_title'], $res2['media_id'], $contactWayInfo['conclusions_miniprogram_appid'], $contactWayInfo['conclusions_miniprogram_page']);
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
        $modelContactWay->recordConfigId($contactWayInfo['_id'], $res, time());
        return $res;
    }

    // 发送新客户欢迎语
    public function sendWelcomeMsg($welcome_code, $welcomeMsgInfo)
    {
        $modelWelcomeMsg = new \App\Qyweixin\Models\ExternalContact\WelcomeMsg();

        $welcomeMsg = new \Qyweixin\Model\ExternalContact\WelcomeMsg($welcome_code);
        $text = new \Qyweixin\Model\ExternalContact\Conclusion\Text($welcomeMsgInfo['text_content']);
        $welcomeMsg->text = $text;

        if (!empty($welcomeMsgInfo['image_media'])) {
            if (!empty($welcomeMsgInfo['image_pic_url'])) {
                $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image("", $welcomeMsgInfo['image_pic_url']);
            } else {
                $res = $this->uploadMediaByApi($welcomeMsgInfo['image_media'], "image", $welcomeMsgInfo['image_media_id'], $welcomeMsgInfo['image_media_created_at']);
                // 发生了改变就更新
                if ($res['media_id'] != $welcomeMsgInfo['image_media_id']) {
                    $modelWelcomeMsg->recordMediaId($welcomeMsgInfo['_id'], $res, time());
                }
                $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image($res['media_id'], "");
            }
            $welcomeMsg->image = $image;
        }

        if (!empty($welcomeMsgInfo['link_url'])) {
            $link = new \Qyweixin\Model\ExternalContact\Conclusion\Link($welcomeMsgInfo['link_title'], $welcomeMsgInfo['link_picurl'], $welcomeMsgInfo['link_desc'], $welcomeMsgInfo['link_url']);
            $welcomeMsg->link = $link;
        }

        if (!empty($welcomeMsgInfo['miniprogram_appid'])) {
            if (!empty($welcomeMsgInfo['miniprogram_pic_media'])) {
                $res2 = $this->uploadMediaByApi($welcomeMsgInfo['miniprogram_pic_media'], "image", $welcomeMsgInfo['miniprogram_pic_media_id'], $welcomeMsgInfo['miniprogram_pic_media_created_at']);
                // 发生了改变就更新
                if ($res2['media_id'] != $welcomeMsgInfo['miniprogram_pic_media_id']) {
                    $modelWelcomeMsg->recordMediaId4Miniprogram($welcomeMsgInfo['_id'], $res2, time());
                }
            }
            $miniprogram = new \Qyweixin\Model\ExternalContact\Conclusion\Miniprogram($welcomeMsgInfo['miniprogram_title'], $res2['media_id'], $welcomeMsgInfo['miniprogram_appid'], $welcomeMsgInfo['miniprogram_page']);
            $welcomeMsg->miniprogram = $miniprogram;
        }

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->sendWelcomeMsg($welcomeMsg);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $updateData = array();
        $updateData['send_time'] = \App\Common\Utils\Helper::getCurrentTime(time());
        $modelWelcomeMsg->update(array('_id' => $welcomeMsgInfo['_id']), array('$set' => $updateData));

        return $res;
    }

    // 添加群欢迎语素材
    public function addGroupWelcomeTemplate($groupWelcomeTemplateInfo)
    {
        $modelGroupWelcomeTemplate = new \App\Qyweixin\Models\ExternalContact\GroupWelcomeTemplate();

        $groupWelcomeTemplate = new \Qyweixin\Model\ExternalContact\GroupWelcomeTemplate();

        $text = new \Qyweixin\Model\ExternalContact\Conclusion\Text($groupWelcomeTemplateInfo['text_content']);
        $groupWelcomeTemplate->text = $text;

        if (!empty($groupWelcomeTemplateInfo['image_media'])) {
            if (!empty($groupWelcomeTemplateInfo['image_pic_url'])) {
                $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image("", $groupWelcomeTemplateInfo['image_pic_url']);
            } else {
                $res = $this->uploadMediaByApi($groupWelcomeTemplateInfo['image_media'], "image", $groupWelcomeTemplateInfo['image_media_id'], $groupWelcomeTemplateInfo['image_media_created_at']);
                // 发生了改变就更新
                if ($res['media_id'] != $groupWelcomeTemplateInfo['image_media_id']) {
                    $modelGroupWelcomeTemplate->recordMediaId($groupWelcomeTemplateInfo['_id'], $res, time());
                }
                $image = new \Qyweixin\Model\ExternalContact\Conclusion\Image($res['media_id'], "");
            }
            $groupWelcomeTemplate->image = $image;
        }

        if (!empty($groupWelcomeTemplateInfo['link_url'])) {
            $link = new \Qyweixin\Model\ExternalContact\Conclusion\Link($groupWelcomeTemplateInfo['link_title'], $groupWelcomeTemplateInfo['link_picurl'], $groupWelcomeTemplateInfo['link_desc'], $groupWelcomeTemplateInfo['link_url']);
            $groupWelcomeTemplate->link = $link;
        }

        if (!empty($groupWelcomeTemplateInfo['miniprogram_appid'])) {
            if (!empty($groupWelcomeTemplateInfo['miniprogram_pic_media'])) {
                $res2 = $this->uploadMediaByApi($groupWelcomeTemplateInfo['miniprogram_pic_media'], "image", $groupWelcomeTemplateInfo['miniprogram_pic_media_id'], $groupWelcomeTemplateInfo['miniprogram_pic_media_created_at']);
                // 发生了改变就更新
                if ($res2['media_id'] != $groupWelcomeTemplateInfo['miniprogram_pic_media_id']) {
                    $modelGroupWelcomeTemplate->recordMediaId4Miniprogram($groupWelcomeTemplateInfo['_id'], $res2, time());
                }
            }
            $miniprogram = new \Qyweixin\Model\ExternalContact\Conclusion\Miniprogram($groupWelcomeTemplateInfo['miniprogram_title'], $res2['media_id'], $groupWelcomeTemplateInfo['miniprogram_appid'], $groupWelcomeTemplateInfo['miniprogram_page']);
            $groupWelcomeTemplate->miniprogram = $miniprogram;
        }

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupWelcomeTemplateManager()->add($groupWelcomeTemplate);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelGroupWelcomeTemplate->recordTemplateId($groupWelcomeTemplateInfo['_id'], $res, time());
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

    // 读取成员
    public function getUserInfo($userInfo)
    {
        $modelUser = new \App\Qyweixin\Models\User\User();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->get($userInfo['userid']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userid": "zhangsan",
         * "name": "李四",
         * "department": [1, 2],
         * "order": [1, 2],
         * "position": "后台工程师",
         * "mobile": "13800000000",
         * "gender": "1",
         * "email": "zhangsan@gzdev.com",
         * "is_leader_in_dept": [1, 0],
         * "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0",
         * "thumb_avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/100",
         * "telephone": "020-123456",
         * "alias": "jackzhang",
         * "address": "广州市海珠区新港中路",
         * "open_userid": "xxxxxx",
         * "main_department": 1,
         * "extattr": {
         * "attrs": [
         * {
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * }
         * ]
         * },
         * "status": 1,
         * "qr_code": "https://open.work.weixin.qq.com/wwopen/userQRCode?vcode=xxx",
         * "external_position": "产品经理",
         * "external_profile": {
         * "external_corp_name": "企业简称",
         * "external_attr": [{
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * },
         * {
         * "type": 2,
         * "name": "测试app",
         * "miniprogram": {
         * "appid": "wx8bd80126147dFAKE",
         * "pagepath": "/index",
         * "title": "my miniprogram"
         * }
         * }
         * ]
         * }
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUser->updateUserInfoById($userInfo, $res);
        return $res;
    }

    // userid转openid
    public function convertToOpenid($userInfo)
    {
        $modelUser = new \App\Qyweixin\Models\User\User();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->convertToOpenid($userInfo['userid']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "openid": "oDjGHs-1yCnGrRovBj2yHij5JAAA"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $data = array();
        $data['openid'] = $res['openid'];
        $modelUser->update(array('_id' => $userInfo['_id']), array('$set' => $data));
        return $res;
    }

    // openid转userid
    public function convertToUserid($userInfo)
    {
        $modelUser = new \App\Qyweixin\Models\User\User();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->convertToUserid($userInfo['openid']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userid": "zhangsan"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $data = array();
        $data['userid'] = $res['userid'];
        $modelUser->update(array('_id' => $userInfo['_id']), array('$set' => $data));
        return $res;
    }

    // 获取加入企业二维码
    public function getJoinQrcode($qrcodeInfo)
    {
        $modelCorpJoinQrcode = new \App\Qyweixin\Models\Contact\CorpJoinQrcode();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->corpGetJoinQrcode($qrcodeInfo['size_type']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "join_qrcode": "https://work.weixin.qq.com/wework_admin/genqrcode?action=join&vcode=3db1fab03118ae2aa1544cb9abe84&r=hb_share_api_mjoin&qr_size=3"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCorpJoinQrcode->recordJoinQrcode($qrcodeInfo['_id'], $res, time());
        return $res;
    }

    // 批量邀请成员
    public function batchInvite($batchInviteInfo)
    {
        $modelBatchInvite = new \App\Qyweixin\Models\Contact\BatchInvite();
        $user = empty($batchInviteInfo['user']) ? array() : (is_array($batchInviteInfo['user']) ? $batchInviteInfo['user'] : \json_decode($batchInviteInfo['user'], true));
        $party = empty($batchInviteInfo['party']) ? array() : (is_array($batchInviteInfo['party']) ? $batchInviteInfo['party'] : \json_decode($batchInviteInfo['party'], true));
        $tag = empty($batchInviteInfo['tag']) ? array() : (is_array($batchInviteInfo['tag']) ? $batchInviteInfo['tag'] : \json_decode($batchInviteInfo['tag'], true));
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->batchInvite($user, $party, $tag);
        /**
         * {
         * "errcode" : 0,
         * "errmsg" : "ok",
         * "invaliduser" : ["UserID1", "UserID2"],
         * "invalidparty" : [PartyID1, PartyID2],
         * "invalidtag": [TagID1, TagID2]
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelBatchInvite->recordResult($batchInviteInfo['_id'], $res, time());
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

        // 交换一下
        $tmp1 = $ToUserName;
        $ToUserName = $FromUserName;
        $FromUserName = $tmp1;

        switch ($match['reply_msg_type']) {
            case 'news':
                $articles = array();
                // 获取图文列表
                $isFirst = empty($articles) ? true : false;
                $modelReplyMsgNews = new \App\Qyweixin\Models\ReplyMsg\News();
                $articles1 = $modelReplyMsgNews->getArticlesByReplyMsgId($replyMsgs[0]['_id'], 'news', $isFirst);
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
        $modelReplyMsgSendLog->record($replyMsgs[0]['provider_appid'], $replyMsgs[0]['authorizer_appid'], $replyMsgs[0]['agentid'], $replyMsgs[0]['_id'], $replyMsgs[0]['name'], $replyMsgs[0]['msg_type'], $replyMsgs[0]['media'], $replyMsgs[0]['media_id'], $replyMsgs[0]['thumb_media'], $replyMsgs[0]['thumb_media_id'], $replyMsgs[0]['title'], $replyMsgs[0]['description'], $replyMsgs[0]['music'], $replyMsgs[0]['hqmusic'], $replyMsgs[0]['kf_account'], $match['_id'], $match['keyword'], $match['reply_msg_type'], $ToUserName, $FromUserName, $replymsg, time());

        return $replymsg;
    }

    public function answerAgentMsgs($FromUserName, $ToUserName, $match)
    {
        $modelAgentMsg = new \App\Qyweixin\Models\AgentMsg\AgentMsg();
        $agentMsgs = $modelAgentMsg->getAgentMsgsByKeyword($match);
        if (empty($agentMsgs)) {
            return false;
        }

        $sendRet = $this->sendAgentMsg($ToUserName, $FromUserName,  $agentMsgs[0], $match);
        return $sendRet['is_ok'];
    }

    public function sendAgentMsg($FromUserName, $ToUserName, $agentMsgInfo, $match)
    {
        $is_ok = false;
        $objQyWeixin = $this->getQyWeixinObject();
        $agentmsg = array();
        $agentid = $agentMsgInfo['agentid'];
        try {
            switch ($match['agent_msg_type']) {
                case 'text':
                    $objMsg = new \Qyweixin\Model\Message\Text($agentid, $agentMsgInfo['description'], $ToUserName);
                    break;
                case 'voice':
                    $media_id = $this->getMediaId4AgentMsg('voice', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\Voice($agentid, $media_id, $ToUserName);
                    break;
                case 'video':
                    $media_id = $this->getMediaId4AgentMsg('video', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\Video($agentid, $media_id, $ToUserName);
                    $objMsg->title = $agentMsgInfo['title'];
                    $objMsg->description = $agentMsgInfo['description'];
                    break;
                case 'file':
                    $media_id = $this->getMediaId4AgentMsg('file', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\File($agentid, $media_id, $ToUserName);
                    break;
                case 'image':
                    $media_id = $this->getMediaId4AgentMsg('image', $agentMsgInfo);
                    $objMsg = new \Qyweixin\Model\Message\Image($agentid, $media_id, $ToUserName);
                    break;
                case 'textcard':
                    $objMsg = new \Qyweixin\Model\Message\TextCard($agentid, $agentMsgInfo['title'], $agentMsgInfo['description'], $agentMsgInfo['url'], $ToUserName);
                    $objMsg->btntxt = $agentMsgInfo['btntxt'];
                    break;
                case 'news':
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelAgentMsgNews = new \App\Qyweixin\Models\AgentMsg\News();
                    $articles1 = $modelAgentMsgNews->getArticlesByAgentMsgId($agentMsgInfo['_id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $objMsg = new \Qyweixin\Model\Message\News($agentid, $articles, $ToUserName);
                    break;
                case 'mpnews':
                    // 获取图文列表
                    $isFirst = false;
                    $modelAgentMsgNews = new \App\Qyweixin\Models\AgentMsg\News();
                    $articles = $modelAgentMsgNews->getArticlesByAgentMsgId($agentMsgInfo['_id'], 'mpnews', $isFirst);
                    if (empty($articles)) {
                        throw new \Exception("应用消息ID:{$agentMsgInfo['agent_msg_id']}所对应的图文不存在");
                    }
                    foreach ($articles as &$article) {
                        $res4ThumbMedia = $this->uploadMedia($article['thumb_media']);
                        $article['thumb_media_id'] = $res4ThumbMedia['media_id'];
                        unset($article['thumb_media']);
                    }
                    $objMsg = new \Qyweixin\Model\Message\Mpnews($agentid, $articles, $ToUserName);
                    break;
                case 'markdown':
                    $objMsg = new \Qyweixin\Model\Message\Markdown($agentid, $agentMsgInfo['description'], $ToUserName);
                    break;
                case 'miniprogram_notice':
                    $objMsg = new \Qyweixin\Model\Message\MiniprogramNotice($agentMsgInfo['appid'], $agentMsgInfo['title'], $ToUserName);
                    $objMsg->page = $agentMsgInfo['pagepath'];
                    $objMsg->description = $agentMsgInfo['description'];
                    $objMsg->emphasis_first_item = $agentMsgInfo['emphasis_first_item'];
                    $objMsg->content_item = $agentMsgInfo['content_item'];
                    break;
                case 'taskcard':
                    $objMsg = new \Qyweixin\Model\Message\TaskCard($agentid, $agentMsgInfo['title'], $agentMsgInfo['description'], $agentMsgInfo['task_id'], $agentMsgInfo['btn'], $ToUserName);
                    $objMsg->url = $agentMsgInfo['url'];
                    break;
                default:
                    throw new \Exception('msg_type:' . $match['agent_msg_type'] . '的消息的发送功能未实现');
                    break;
            }
            $objMsg->touser = $ToUserName;
            $objMsg->safe = intval($agentMsgInfo['safe']);
            $objMsg->enable_id_trans = intval($agentMsgInfo['enable_id_trans']);
            $objMsg->enable_duplicate_check = intval($agentMsgInfo['enable_duplicate_check']);
            $objMsg->duplicate_check_interval = intval($agentMsgInfo['duplicate_check_interval']);

            $agentmsg = $objQyWeixin->getMessageManager()->send($objMsg);
            if (!empty($agentmsg['errcode'])) {
                throw new \Exception($agentmsg['errmsg'], $agentmsg['errcode']);
            }
            $is_ok = true;
        } catch (\Exception $e) {
            $agentmsg['errorcode'] = $e->getCode();
            $agentmsg['errormsg'] = $e->getMessage();
        }

        if (empty($agentmsg)) {
            $agentmsg = array();
        }
        // 记录日志
        $modelAgentMsgSendLog = new \App\Qyweixin\Models\AgentMsg\SendLog();
        $modelAgentMsgSendLog->record(
            $agentMsgInfo['provider_appid'],
            $agentMsgInfo['authorizer_appid'],
            $agentMsgInfo['agentid'],
            $agentMsgInfo['_id'],
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
            $match['_id'],
            $match['keyword'],
            $match['agent_msg_type'],
            $ToUserName,
            $FromUserName,
            \\App\Common\Utils\Helper::myJsonEncode($agentmsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $agentmsg
        );
    }

    public function sendAppchatMsg($FromUserName, $ToUserName, $appchatMsgInfo, $match)
    {
        $is_ok = false;
        $objQyWeixin = $this->getQyWeixinObject();
        $appchatmsg = array();
        $agentid = $appchatMsgInfo['agentid'];
        $chatid = $ToUserName; //$appchatMsgInfo['chatid'];
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
                    $articles1 = $modelAppchatMsgNews->getArticlesByAppchatMsgId($appchatMsgInfo['_id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $objMsg = new \Qyweixin\Model\AppchatMsg\News($chatid, $articles);
                    break;
                case 'mpnews':
                    // 获取图文列表
                    $isFirst = false;
                    $modelAppchatMsgNews = new \App\Qyweixin\Models\AppchatMsg\News();
                    $articles = $modelAppchatMsgNews->getArticlesByAppchatMsgId($appchatMsgInfo['_id'], 'mpnews', $isFirst);
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
                default:
                    throw new \Exception('msg_type:' . $match['appchat_msg_type'] . '的消息的发送功能未实现');
                    break;
            }

            $objMsg->safe = intval($appchatMsgInfo['safe']);
            $appchatmsg = $objQyWeixin->getAppchatManager()->send($objMsg);
            if (!empty($appchatmsg['errcode'])) {
                throw new \Exception($appchatmsg['errmsg'], $appchatmsg['errcode']);
            }
            $is_ok = true;
        } catch (\Exception $e) {
            $appchatmsg['errorcode'] = $e->getCode();
            $appchatmsg['errormsg'] = $e->getMessage();
        }

        if (empty($appchatmsg)) {
            $appchatmsg = array();
        }
        // 记录日志
        $modelAppchatMsgSendLog = new \App\Qyweixin\Models\AppchatMsg\SendLog();
        $modelAppchatMsgSendLog->record(
            $appchatMsgInfo['provider_appid'],
            $appchatMsgInfo['authorizer_appid'],
            $appchatMsgInfo['agentid'],
            $appchatMsgInfo['chatid'],
            $appchatMsgInfo['_id'],
            $appchatMsgInfo['name'],
            $appchatMsgInfo['msg_type'],
            $appchatMsgInfo['media'],
            $appchatMsgInfo['media_id'],
            $appchatMsgInfo['title'],
            $appchatMsgInfo['description'],
            $appchatMsgInfo['url'],
            $appchatMsgInfo['btntxt'],
            $appchatMsgInfo['safe'],
            $match['_id'],
            $match['keyword'],
            $match['appchat_msg_type'],
            $ToUserName,
            $FromUserName,
            \\App\Common\Utils\Helper::myJsonEncode($appchatmsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $appchatmsg
        );
    }

    public function sendLinkedcorpMsg($FromUserName, $ToUserName, $linkedcorpMsgInfo, $match)
    {
        $is_ok = false;
        $objQyWeixin = $this->getQyWeixinObject();
        $linkedcorpmsg = array();
        $agentid = $linkedcorpMsgInfo['agentid'];
        try {
            switch ($match['linkedcorp_msg_type']) {
                case 'text':
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\Text($agentid, $linkedcorpMsgInfo['description']);
                    break;
                case 'voice':
                    $media_id = $this->getMediaId4LinkedcorpMsg('voice', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\Voice($agentid, $media_id);
                    break;
                case 'video':
                    $media_id = $this->getMediaId4LinkedcorpMsg('video', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\Video($agentid, $media_id);
                    $objMsg->title = $linkedcorpMsgInfo['title'];
                    $objMsg->description = $linkedcorpMsgInfo['description'];
                    break;
                case 'file':
                    $media_id = $this->getMediaId4LinkedcorpMsg('file', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\File($agentid, $media_id);
                    break;
                case 'image':
                    $media_id = $this->getMediaId4LinkedcorpMsg('image', $linkedcorpMsgInfo);
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\Image($agentid, $media_id);
                    break;
                case 'textcard':
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\TextCard($agentid, $linkedcorpMsgInfo['title'], $linkedcorpMsgInfo['description'], $linkedcorpMsgInfo['url']);
                    $objMsg->btntxt = $linkedcorpMsgInfo['btntxt'];
                    break;
                case 'news':
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelLinkedcorpMsgNews = new \App\Qyweixin\Models\LinkedcorpMsg\News();
                    $articles1 = $modelLinkedcorpMsgNews->getArticlesByLinkedcorpMsgId($linkedcorpMsgInfo['_id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\News($agentid, $articles);
                    break;
                case 'mpnews':
                    // 获取图文列表
                    $isFirst = false;
                    $modelLinkedcorpMsgNews = new \App\Qyweixin\Models\LinkedcorpMsg\News();
                    $articles = $modelLinkedcorpMsgNews->getArticlesByLinkedcorpMsgId($linkedcorpMsgInfo['_id'], 'mpnews', $isFirst);
                    if (empty($articles)) {
                        throw new \Exception("互联企业消息ID:{$linkedcorpMsgInfo['linkedcorp_msg_id']}所对应的图文不存在");
                    }
                    foreach ($articles as &$article) {
                        $res4ThumbMedia = $this->uploadMedia($article['thumb_media']);
                        $article['thumb_media_id'] = $res4ThumbMedia['media_id'];
                        unset($article['thumb_media']);
                    }
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\Mpnews($agentid, $articles);
                    break;
                case 'markdown':
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\Markdown($agentid, $linkedcorpMsgInfo['description']);
                    break;
                case 'miniprogram_notice':
                    $objMsg = new \Qyweixin\Model\Linkedcorp\Message\MiniprogramNotice($linkedcorpMsgInfo['appid'], $linkedcorpMsgInfo['title']);
                    $objMsg->page = $linkedcorpMsgInfo['pagepath'];
                    $objMsg->description = $linkedcorpMsgInfo['description'];
                    $objMsg->emphasis_first_item = $linkedcorpMsgInfo['emphasis_first_item'];
                    $objMsg->content_item = $linkedcorpMsgInfo['content_item'];
                    break;
                default:
                    throw new \Exception('msg_type:' . $match['linkedcorp_msg_type'] . '的消息的发送功能未实现');
                    break;
            }

            $objMsg->touser = array($ToUserName);
            $objMsg->toall = intval($linkedcorpMsgInfo['toall']);
            $objMsg->safe = intval($linkedcorpMsgInfo['safe']);
            $linkedcorpmsg = $objQyWeixin->getLinkedcorpManager()->getMessageManager()->send($objMsg);
            if (!empty($linkedcorpmsg['errcode'])) {
                throw new \Exception($linkedcorpmsg['errmsg'], $linkedcorpmsg['errcode']);
            }
            $is_ok = true;
        } catch (\Exception $e) {
            $linkedcorpmsg['errorcode'] = $e->getCode();
            $linkedcorpmsg['errormsg'] = $e->getMessage();
        }

        if (empty($linkedcorpmsg)) {
            $linkedcorpmsg = array();
        }
        // 记录日志
        $modelLinkedcorpMsgSendLog = new \App\Qyweixin\Models\LinkedcorpMsg\SendLog();
        $modelLinkedcorpMsgSendLog->record(
            $linkedcorpMsgInfo['provider_appid'],
            $linkedcorpMsgInfo['authorizer_appid'],
            $linkedcorpMsgInfo['agentid'],
            $linkedcorpMsgInfo['_id'],
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
            $match['_id'],
            $match['keyword'],
            $match['linkedcorp_msg_type'],
            $ToUserName,
            $FromUserName,
            \\App\Common\Utils\Helper::myJsonEncode($linkedcorpmsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $linkedcorpmsg
        );
    }

    //获取部门列表
    public function getDepartmentList($dep_id)
    {
        $modelDepartment = new \App\Qyweixin\Models\Contact\Department();
        $res = $this->getQyWeixinObject()
            ->getDepartmentManager()
            ->getDepartmentList($dep_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "department": [
         *  {
         *      "id": 2,
         *      "name": "广州研发中心",
         *      "name_en": "RDGZ",
         *      "parentid": 1,
         *      "order": 10
         *  },
         *  {
         *      "id": 3,
         *      "name": "邮箱产品部",
         *      "name_en": "mail",
         *      "parentid": 2,
         *      "order": 40
         *  }
         *]
         * }
         */
        // 如果从跟部门进行同步的话 那么先将所有的记录is_exist改成0
        if (empty($dep_id)) {
            $modelDepartment->clearExist($this->authorizer_appid, $this->provider_appid);
        }
        $modelDepartment->syncDepartmentList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取部门成员
    public function getDepartmentUserSimplelist($dep_id, $fetch_child = 0, $is_root = false)
    {
        $modelDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->simplelist($dep_id, $fetch_child);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userlist": [
         * {
         * "userid": "zhangsan",
         * "name": "李四",
         * "department": [1, 2],
         * "open_userid": "xxxxxx"
         * }
         * ]}
         */
        if (!empty($is_root) && !empty($fetch_child)) {
            $modelDepartmentUser->clearExist($this->authorizer_appid, $this->provider_appid);
        }
        $modelDepartmentUser->syncDepartmentUserList($dep_id, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取部门成员详情
    public function getDepartmentUserDetaillist($dep_id, $fetch_child = 0, $is_root = false)
    {
        $modelUser = new \App\Qyweixin\Models\User\User();
        $modelDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->userlist($dep_id, $fetch_child);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userlist": [{
         * "userid": "zhangsan",
         * "name": "李四",
         * "department": [1, 2],
         * "order": [1, 2],
         * "position": "后台工程师",
         * "mobile": "13800000000",
         * "gender": "1",
         * "email": "zhangsan@gzdev.com",
         * "is_leader_in_dept": [1, 0],
         * "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0",
         * "thumb_avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/100",
         * "telephone": "020-123456",
         * "alias": "jackzhang",
         * "status": 1,
         * "address": "广州市海珠区新港中路",
         * "hide_mobile" : 0,
         * "english_name" : "jacky",
         * "open_userid": "xxxxxx",
         * "main_department": 1,
         * "extattr": {
         * "attrs": [
         * {
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * }
         * ]
         * },
         * "qr_code": "https://open.work.weixin.qq.com/wwopen/userQRCode?vcode=xxx",
         * "external_position": "产品经理",
         * "external_profile": {
         * "external_corp_name": "企业简称",
         * "external_attr": [{
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * },
         * {
         * "type": 2,
         * "name": "测试app",
         * "miniprogram": {
         * "appid": "wx8bd80126147dFAKE",
         * "pagepath": "/index",
         * "title": "miniprogram"
         * }
         * }
         * ]
         * }
         * }]
         * }
         */
        if (!empty($is_root) && !empty($fetch_child)) {
            $modelDepartmentUser->clearExist($this->authorizer_appid, $this->provider_appid);
        }
        $now = time();
        $modelDepartmentUser->syncDepartmentUserList($this->authorizer_appid, $this->provider_appid, $res, $now);
        $modelUser->syncUserList($this->authorizer_appid, $this->provider_appid, $res, $now);

        return $res;
    }

    //获取标签列表
    public function getTagList()
    {
        $modelTag = new \App\Qyweixin\Models\Contact\Tag();
        $res = $this->getQyWeixinObject()
            ->getTagManager()
            ->getTagList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "taglist":[
         *      {"tagid":1,"tagname":"a"},
         *      {"tagid":2,"tagname":"b"}
         *  ]
         * }
         */
        $modelTag->syncTagList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取标签成员
    public function getTag($tagid)
    {
        $res = $this->getQyWeixinObject()
            ->getTagManager()
            ->get($tagid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "tagname": "乒乓球协会",
         * "userlist": [
         * {
         *  "userid": "zhangsan",
         *  "name": "李四"
         * }
         * ],
         * "partylist": [2]
         * }
         */

        $modelTagParty = new \App\Qyweixin\Models\Contact\TagParty();
        $modelTagUser = new \App\Qyweixin\Models\Contact\TagUser();
        $now = time();
        $modelTagParty->syncTagDepartmentList($tagid, $this->authorizer_appid, $this->provider_appid, $res, $now);
        $modelTagUser->syncTagUserList($tagid, $this->authorizer_appid, $this->provider_appid, $res, $now);
        return $res;
    }

    //获取企业活跃成员数
    public function getActiveStat($start_time)
    {
        $modelUserActiveStat = new \App\Qyweixin\Models\Contact\UserActiveStat();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->getActiveStat($start_time);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "active_cnt":100
         * }
         */
        $modelUserActiveStat->syncActiveStat($start_time, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
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
            ->getExternalContactList($userid);
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
    public function getExternalUserInfo($userInfo)
    {
        $modelExternalUser = new \App\Qyweixin\Models\ExternalContact\ExternalUser();
        $external_userid = $userInfo['external_userid'];

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

    //修改客户备注信息
    public function remarkExternalUser($userInfo4Remark)
    {
        $modelExternalUserRemark = new \App\Qyweixin\Models\ExternalContact\ExternalUserRemark();
        $remark = new \Qyweixin\Model\ExternalContact\Remark($userInfo4Remark['userid'], $userInfo4Remark['external_userid']);
        $remark->remark = $userInfo4Remark['remark'];
        $remark->description = $userInfo4Remark['description'];
        $remark->remark_company = $userInfo4Remark['remark_company'];
        if (!empty($userInfo4Remark['remark_mobiles'])) {
            $remark->remark_mobiles = \json_decode($userInfo4Remark['remark_mobiles'], true);
        }

        if (!empty($userInfo4Remark['remark_pic_media'])) {
            $res = $this->uploadMediaByApi($userInfo4Remark['remark_pic_media'], "image", $userInfo4Remark['remark_pic_mediaid'], $userInfo4Remark['remark_pic_media_created_at']);
            // 发生了改变就更新
            if ($res['media_id'] != $userInfo4Remark['remark_pic_mediaid']) {
                $modelExternalUserRemark->recordMediaId($userInfo4Remark['_id'], $res, time());
            }
            $remark->remark_pic_mediaid = $res['media_id'];
        }

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->remark($remark);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok"
         */
        $updateData = array();
        $updateData['update_remark_time'] = \App\Common\Utils\Helper::getCurrentTime(time());
        $modelExternalUserRemark->update(array('_id' => $userInfo4Remark['_id']), array('$set' => $updateData));
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

    //编辑客户企业标签
    public function markCorpTag($corpTag4Mark)
    {
        $modelCorpTagMark = new \App\Qyweixin\Models\ExternalContact\CorpTagMark();

        $add_tag = array();
        if (!empty($corpTag4Mark['add_tag'])) {
            $add_tag = \json_decode($corpTag4Mark['add_tag'], true);
        }

        $remove_tag = array();
        if (!empty($corpTag4Mark['remove_tag'])) {
            $remove_tag = \json_decode($corpTag4Mark['remove_tag'], true);
        }

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->markTag($corpTag4Mark['userid'], $corpTag4Mark['external_userid'], $add_tag, $remove_tag);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok"
         */
        $updateData = array();
        $updateData['mark_tag_time'] = \App\Common\Utils\Helper::getCurrentTime(time());
        $modelCorpTagMark->update(array('_id' => $corpTag4Mark['_id']), array('$set' => $updateData));

        return $res;
    }

    //获取客户群列表
    public function getGroupChatList($status_filter = 0, $owner_filter = array(), $offset = 0, $limit = 1000)
    {
        $modelGroupChat = new \App\Qyweixin\Models\ExternalContact\GroupChat();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->getGroupchatList($status_filter, $owner_filter, $offset, $limit);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "group_chat_list": [{
         *      "chat_id": "wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
         *      "status": 0
         *   }, {
         *      "chat_id": "wrOgQhDgAAcwMTB7YmDkbeBsAAAA",
         *      "status": 0
         *  }]
         * }
         */
        $modelGroupChat->syncGroupChatList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    //获取客户群详情
    public function getGroupChatInfo($groupChatInfo)
    {
        $modelGroupChat = new \App\Qyweixin\Models\ExternalContact\GroupChat();
        $chatid = $groupChatInfo['chat_id'];

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->get($chatid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "group_chat": {
         *      "chat_id": "wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
         *      "name": "销售客服群",
         *      "owner": "ZhuShengBen",
         *      "create_time": 1572505490,
         *       "notice" : "文明沟通，拒绝脏话",
         *      "member_list": [{
         *          "userid": "abel",
         *          "type": 1,
         *          "join_time": 1572505491,
         *          "join_scene": 1
         *       }, {
         *           "userid": "sam",
         *          "type": 1,
         *          "join_time": 1572505491,
         *           "join_scene": 1
         *      }, {
         *           "userid": "wmOgQhDgAAuXFJGwbve4g4iXknfOAAAA",
         *           "type": 2,
         *          "join_time": 1572505491,
         *          "join_scene": 1
         *      }]
         *  }
         */
        $modelGroupChat->updateGroupChatInfoByApi($groupChatInfo, $res, time());

        // 同步member_list
        if (!empty($res['group_chat']['member_list'])) {
            $modelGroupChatMember = new \App\Qyweixin\Models\ExternalContact\GroupChatMember();
            $modelGroupChatMember->syncMemberList($chatid, $this->authorizer_appid, $this->provider_appid, $res, time());
        }

        return $res;
    }

    // 获取客户朋友圈全部的发表记录
    public function getMomentList($start_time, $end_time, $creator = "", $filter_type = 2, $limit = 100, $cursor = "")
    {
        $modelMoment = new \App\Qyweixin\Models\ExternalContact\Moment();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getMomentManager()->getMomentList($start_time, $end_time, $creator, $filter_type, $limit, $cursor);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMoment->syncMomentList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    //获取离职成员的客户列表
    public function getUnassignedList($page_id = 0, $page_size = 1000)
    {
        $modelUnassigned = new \App\Qyweixin\Models\ExternalContact\Unassigned();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getUnassignedList($page_id, $page_size);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         *   "errcode":0,
         *   "errmsg":"ok",
         *   "info":[
         *   {
         *        "handover_userid":"zhangsan",
         *        "external_userid":"woAJ2GCAAAd4uL12hdfsdasassdDmAAAAA",
         *        "dimission_time":1550838571
         *   },
         *   {
         *        "handover_userid":"lisi",
         *        "external_userid":"wmAJ2GCAAAzLTI123ghsdfoGZNqqAAAA",
         *        "dimission_time":1550661468
         *    }
         * ],
         * "is_last":false
         *}
         */
        $modelUnassigned->syncUnassignedList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    //离职成员的外部联系人再分配
    public function transfer($transferInfo)
    {
        $modelTransfer = new \App\Qyweixin\Models\ExternalContact\Transfer();

        $external_userid = $transferInfo['external_userid'];
        $handover_userid = $transferInfo['handover_userid'];
        $takeover_userid = $transferInfo['takeover_userid'];
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->transfer($external_userid, $handover_userid, $takeover_userid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok"
         */
        $modelTransfer->recordTransferResult($transferInfo['_id'], $res, $res, time());
        return $res;
    }

    //离职成员的群再分配
    public function groupChatTransfer($transferInfo)
    {
        $modelGroupChatTransfer = new \App\Qyweixin\Models\ExternalContact\GroupChatTransfer();
        $chat_id = $transferInfo['chat_id'];
        $new_owner = $transferInfo['new_owner'];

        $chat_id_list = array($chat_id);
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->transfer($chat_id_list, $new_owner);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok"
         * "failed_chat_list": [
         * {
         *     "chat_id": "wrOgQhDgAAcwMTB7YmDkbeBsgT_KAAAA",
         *    "errcode": 90500,
         *    "errmsg": "the owner of this chat is not resigned"
         * }
         * ]
         */
        $modelGroupChatTransfer->recordTransferResult($transferInfo['_id'], $res, $res, time());
        return $res;
    }

    //获取联系客户统计数据
    public function getUserBehaviorDataByUserId($userid, $start_time, $end_time)
    {
        $modelUserBehaviorDataByUserid = new \App\Qyweixin\Models\ExternalContact\UserBehaviorDataByUserid();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getUserBehaviorData(array($userid), array(), $start_time, $end_time);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         *     "behavior_data":
         *      [
         *          {
         *          "stat_time":1536508800,
         *          "chat_cnt":100,
         *          "message_cnt":80,
         *          "reply_percentage":60.25,
         *          "avg_reply_time":1,
         *          "negative_feedback_cnt":0,
         *          "new_apply_cnt":6,
         *          "new_contact_cnt":5
         *          },
         *          {
         *          "stat_time":1536940800,
         *          "chat_cnt":20,
         *          "message_cnt":40,
         *          "reply_percentage":100,
         *          "avg_reply_time":1,
         *          "negative_feedback_cnt":0,
         *          "new_apply_cnt":6,
         *          "new_contact_cnt":5
         *          }
         *      ]
         */
        $modelUserBehaviorDataByUserid->syncBehaviorDataList($userid, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    //获取客户群统计数据
    public function getGroupChatStatistic($day_begin_time, array $owner_filter, $order_by = 1, $order_asc = 0, $offset = 0, $limit = 1000)
    {
        $modelGroupChatStatisticByUserid = new \App\Qyweixin\Models\ExternalContact\GroupChatStatisticByUserid();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->statistic($day_begin_time, $owner_filter, $order_by, $order_asc, $offset, $limit);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "total": 1,
         * "next_offset": 1,
         * "items": [{
         *        "owner": "zhangsan",
         *        "data": {
         *            "new_chat_cnt": 2,
         *            "chat_total": 2,
         *            "chat_has_msg": 0,
         *            "new_member_cnt": 0,
         *            "member_total": 6,
         *            "member_has_msg": 0,
         *            "msg_total": 0
         *        }
         *    }]
         */
        $modelGroupChatStatisticByUserid->syncGroupchatStatisticList($day_begin_time, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取应用的可见范围
    public function getLinkedcorpAgentPermList()
    {
        $res = $this->getQyWeixinObject()
            ->getLinkedcorpManager()
            ->getAgentManager()->getPermList();
        return $res;
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        return $res;
    }

    // 获取企业的全部群发记录
    public function getGroupmsgList($chat_type, $start_time, $end_time, $creator = "", $filter_type = 2, $limit = 100, $cursor = "")
    {
        $modelMsgTemplate = new \App\Qyweixin\Models\ExternalContact\MsgTemplate();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupMsgManager()->getGroupmsgList($chat_type, $start_time, $end_time, $creator, $filter_type, $limit, $cursor);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMsgTemplate->syncGroupmsgList($this->authorizer_appid, $this->provider_appid, $this->agentid, $chat_type, $res, time());
        return $res;
    }

    // 获取群发成员发送任务列表
    public function getGroupmsgTask($msgid, $limit = 1000, $cursor = "")
    {
        $modelGroupMsgTask = new \App\Qyweixin\Models\ExternalContact\GroupMsgTask();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupMsgManager()->getGroupmsgTask($msgid, $limit, $cursor);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelGroupMsgTask->syncTaskList($msgid, $this->authorizer_appid, $this->provider_appid, $this->agentid, $res, time());
        return $res;
    }

    // 获取企业群发成员执行结果
    public function getGroupMsgSendResult($msgid, $userid, $limit = 1000, $cursor = "")
    {
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupMsgManager()->getGroupMsgSendResult($msgid, $userid, $limit, $cursor);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        //  "next_cursor":"CURSOR",
        // "send_list": [
        // {
        // "external_userid": "wmqfasd1e19278asdasAAAA",
        // "chat_id":"wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
        // "userid": "zhangsan",
        // "status": 1,
        // "send_time": 1552536375
        // }
        // ]

        // 同步数据到结果表
        // 同步detail_list
        if (!empty($res['send_list'])) {
            $modelGroupMsgSendResult = new \App\Qyweixin\Models\ExternalContact\GroupMsgSendResult();
            $modelGroupMsgSendResult->syncDetailList($msgid, $userid, $this->authorizer_appid, $this->provider_appid, $this->agentid, $res, time());
        }
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
