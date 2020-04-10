<?php

namespace App\Weixin2\Services;

class Service1
{

    private $authorizer_appid = "";

    private $component_appid = "";

    private $componentConfig = array();

    private $authorizerConfig = array();

    private $objWeixin = null;

    private $objWeixinComponent = null;

    private $modelWeixinopenComponent;

    private $modelWeixinopenAuthorizer;

    public function __construct($authorizer_appid, $component_appid)
    {
        $this->authorizer_appid = $authorizer_appid;
        $this->component_appid = $component_appid;
        $this->modelWeixinopenComponent = new \App\Weixin2\Models\Component\Component();
        $this->modelWeixinopenAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
    }

    public function getAppConfig4Component()
    {
        $this->getToken4Component();
        return $this->componentConfig;
    }

    public function getWeixinComponent()
    {
        $this->getToken4Component();

        $this->objWeixinComponent = new \Weixin\Component($this->componentConfig['appid'], $this->componentConfig['appsecret']);
        if (!empty($this->componentConfig['access_token'])) {
            $this->objWeixinComponent->setAccessToken($this->componentConfig['access_token']);
        }
        return $this->objWeixinComponent;
    }

    public function getAppConfig4Authorizer()
    {
        $this->getToken4Authorizer();
        return $this->authorizerConfig;
    }

    public function getWeixinObject()
    {
        $this->getToken4Authorizer();

        $this->objWeixin = new \Weixin\Client();
        if (!empty($this->authorizerConfig['access_token'])) {
            $this->objWeixin->setAccessToken($this->authorizerConfig['access_token']);
        }
        return $this->objWeixin;
    }

    protected function getToken4Component()
    {
        if (empty($this->componentConfig)) {
            $this->componentConfig = $this->modelWeixinopenComponent->getTokenByAppid($this->component_appid);
            if (empty($this->componentConfig)) {
                throw new \Exception("component_appid:{$this->component_appid}所对应的记录不存在");
            }
        }
    }

    protected function getToken4Authorizer()
    {
        if (empty($this->authorizerConfig)) {
            $this->authorizerConfig = $this->modelWeixinopenAuthorizer->getTokenByAppid($this->component_appid, $this->authorizer_appid);
            if (empty($this->authorizerConfig)) {
                throw new \Exception("component_appid:{$this->component_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
        }
    }

    public function getAuthorizerInfo()
    {
        $modelAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
        $authorizerInfo = $modelAuthorizer->getInfoByAppid($this->component_appid, $this->authorizer_appid, true);
        if (empty($authorizerInfo)) {
            throw new \Exception("对应的授权方不存在");
        }
        $res = $this->getWeixinComponent()->apiGetAuthorizerInfo($this->authorizer_appid);
        $modelAuthorizer->updateAuthorizerInfo($authorizerInfo['_id'], $res, $authorizerInfo['memo']);
        return $res;
    }

    public function addMaterial($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }
        $description = array();
        if (!empty($materialInfo['title'])) {
            $description['title'] = $materialInfo['title'];
        }
        if (!empty($materialInfo['introduction'])) {
            $description['introduction'] = $materialInfo['introduction'];
        }
        $filePath = $modelMaterial->getPhysicalFilePath($materialInfo['media']);
        $res = $this->getWeixinObject()
            ->getMaterialManager()
            ->addMaterial($materialInfo['type'], $filePath, $description);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMaterial->recordMediaId($material_id, $res, time());
        return $res;
    }

    public function deleteMaterial($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }
        $media_id = $materialInfo['media_id'];

        $res = $this->getWeixinObject()
            ->getMaterialManager()
            ->delMaterial($media_id);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMaterial->removeMediaId($material_id, $res, time());

        return $res;
    }

    public function addNews($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }

        // 查找对应的永久图文素材
        $modelMaterialNews = new \App\Weixin2\Models\Material\News();
        $articles = $modelMaterialNews->getArticlesByMaterialId($material_id, $this->authorizer_appid, $this->component_appid);

        if (empty($articles)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久图文素材不存在");
        }

        // 新增永久图文素材
        $res = $this->getWeixinObject()
            ->getMaterialManager()
            ->addNews($articles);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMaterial->recordMediaId($material_id, $res, time());
        return $res;
    }

    public function updateNews($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }

        // 查找对应的永久图文素材
        $modelMaterialNews = new \App\Weixin2\Models\Material\News();
        $articles = $modelMaterialNews->getArticlesByMaterialId($material_id, $this->authorizer_appid, $this->component_appid);

        if (empty($articles)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久图文素材不存在");
        }

        $media_id = $materialInfo['media_id'];

        foreach ($articles as $index => $article) {
            // 修改增永久图文素材
            $res = $this->getWeixinObject()
                ->getMaterialManager()
                ->updateNews($media_id, $index, $article);
            if (!empty($res['errcode'])) {
                throw new \Exception($res['errmsg'], $res['errcode']);
            }
        }

        $modelMaterial->recordMediaId($material_id, $res, time());
        return $res;
    }

    public function uploadMedia($media_id)
    {
        $modelMedia = new \App\Weixin2\Models\Media\Media();
        $mediaInfo = $modelMedia->getInfoById($media_id);
        if (empty($mediaInfo)) {
            throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材不存在");
        }
        // 媒体文件在微信后台保存时间为3天，即3天后media_id失效。
        $expire_seconds = 24 * 3600 * 2.7;

        // 如果已上传并且没有过期
        if (!empty($mediaInfo['media_id']) && (strtotime($mediaInfo['media_time']) + $expire_seconds) > time()) {
            // throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材是已上传并且没有过期");
            $res['media_id'] = $mediaInfo['media_id'];
            return $res;
        }
        $filePath = $modelMedia->getPhysicalFilePath($mediaInfo['media']);

        $res = $this->getWeixinObject()
            ->getMediaManager()
            ->upload($mediaInfo['type'], $filePath);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [type] => thumb
        // [thumb_media_id] => RjJp_zlifrTf3RPmgSrXpRwdg9WXY31JGrhyz6mVWthYgmna2BgRqSeCnlGF47oY
        // [created_at] => 1557741369
        if (empty($res['media_id']) && !empty($res['thumb_media_id'])) {
            $res['media_id'] = $res['thumb_media_id'];
        }
        $modelMedia->recordMediaId($media_id, $res, time());
        return $res;
    }

    public function createMenu()
    {
        $modelMenu = new \App\Weixin2\Models\Menu\Menu();
        $menus = $modelMenu->buildMenu($this->authorizer_appid, $this->component_appid);
        // return $menus;
        $res = $this->getWeixinObject()
            ->getMenuManager()
            ->create($menus);

        return $res;
    }

    public function createConditionalMenu($matchrule_id)
    {
        $modelMenuConditionalMatchrule = new \App\Weixin2\Models\Menu\ConditionalMatchrule();
        $matchRule = $modelMenuConditionalMatchrule->getInfoById($matchrule_id);
        if (empty($matchRule)) {
            throw new \Exception("匹配规则记录ID:{$matchrule_id}所对应的匹配规则不存在");
        }
        // 检查匹配规则是否有效
        $ruleInfo = $modelMenuConditionalMatchrule->checkMatchRule($matchRule);
        if (empty($ruleInfo)) {
            throw new \Exception("规则名:{$matchRule['matchrule_name']}所对应的匹配规则设置不正确,请至少设置一项");
        }

        // 增加菜单
        $modelMenuConditional = new \App\Weixin2\Models\Menu\Conditional();
        $menusWithMatchrule = $modelMenuConditional->buildMenusWithMatchrule($ruleInfo, $this->authorizer_appid, $this->component_appid);
        // return $menusWithMatchrule;
        $res = $this->getWeixinObject()
            ->getMenuManager()
            ->addconditional($menusWithMatchrule);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMenuConditional->recordMenuId($matchrule_id, $res['menuid'], time());
        return $res;
    }

    public function deleteConditionalMenu($matchrule_id, $menuid)
    {
        $res = $this->getWeixinObject()
            ->getMenuManager()
            ->delconditional($menuid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMenuConditional = new \App\Weixin2\Models\Menu\Conditional();
        $modelMenuConditional->removeMenuId($matchrule_id, $menuid);
        return $res;
    }

    /**
     * 创建二维码
     */
    public function createQrcode($qrcode_id)
    {
        $modelQrcode = new \App\Weixin2\Models\Qrcode\Qrcode();
        $qrcodeInfo = $modelQrcode->getInfoById($qrcode_id);
        if (empty($qrcodeInfo)) {
            throw new \Exception("二维码记录ID:{$qrcode_id}所对应的二维码不存在");
        }
        if (empty($qrcodeInfo['expire_seconds'])) {
            $qrcodeInfo['expire_seconds'] = 0;
        }

        // 如果是永久并且已生成的话
        if (in_array($qrcodeInfo['action_name'], array(
            "QR_LIMIT_SCENE",
            "QR_LIMIT_STR_SCENE"
        )) && !empty($qrcodeInfo['is_created'])) {
            throw new \Exception("二维码记录ID:{$qrcode_id}所对应的二维码是永久二维码并且已生成");
        }
        // 如果是临时并且已生成并且没有过期
        if (in_array($qrcodeInfo['action_name'], array(
            "QR_SCENE",
            "QR_STR_SCENE"
        )) && !empty($qrcodeInfo['is_created']) && (strtotime($qrcodeInfo['ticket_time']) + $qrcodeInfo['expire_seconds']) > time()) {
            throw new \Exception("二维码记录ID:{$qrcode_id}所对应的二维码是临时二维码并且已生成并且没有过期");
        }

        $qrcodeManager = $this->getWeixinObject()->getQrcodeManager();
        $res = $qrcodeManager->create3($qrcodeInfo['action_name'], $qrcodeInfo['scene'], $qrcodeInfo['expire_seconds']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $ticket = \urlencode($res['ticket']);
        $ticket = $qrcodeManager->getQrcodeUrl($ticket);

        $modelQrcode->recordTicket($qrcode_id, $ticket, $res, time());
        return $res;
    }

    public function getUserInfo($user_id)
    {
        $modelUser = new \App\Weixin2\Models\User\User();
        $userInfo = $modelUser->getInfoById($user_id);
        if (empty($userInfo)) {
            throw new \Exception("用户记录ID:{$user_id}所对应的用户不存在");
        }
        $res = $this->getWeixinObject()
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

    public function getUserTagIdList($user_id)
    {
        $modelUser = new \App\Weixin2\Models\User\User();
        $userInfo = $modelUser->getInfoById($user_id);
        if (empty($userInfo)) {
            throw new \Exception("用户记录ID:{$user_id}所对应的用户不存在");
        }
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->userTagList($userInfo['openid']);
        /**
         * {
         * "tagid_list":[128,2]
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUser->updateUserInfoById($userInfo, $res);
        return $res;
    }

    public function addUserTag($user_tag_id)
    {
        $modelUserTag = new \App\Weixin2\Models\User\Tag();
        $userTagInfo = $modelUserTag->getInfoById($user_tag_id);
        if (empty($userTagInfo)) {
            throw new \Exception("用户标签记录ID:{$user_tag_id}所对应的用户标签不存在");
        }
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->create($userTagInfo['name']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * { "tag":{ "id":134,//标签id "name":"广东" } }
         */
        $modelUserTag->recordTagId($user_tag_id, $res, time());
        return $res;
    }

    public function deleteUserTag($user_tag_id)
    {
        $modelUserTag = new \App\Weixin2\Models\User\Tag();
        $userTagInfo = $modelUserTag->getInfoById($user_tag_id);
        if (empty($userTagInfo)) {
            throw new \Exception("用户标签记录ID:{$user_tag_id}所对应的用户标签不存在");
        }
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->delete($userTagInfo['tag_id']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUserTag->removeTagId($user_tag_id);

        return $res;
    }

    public function syncTagList()
    {
        $modelUserTag = new \App\Weixin2\Models\User\Tag();
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->get();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "tags":[{
         * "id":1,
         * "name":"每天一罐可乐星人",
         * "count":0 //此标签下粉丝数
         * },
         * {
         * "id":2,
         * "name":"星标组",
         * "count":0
         * },
         * {
         * "id":127,
         * "name":"广东",
         * "count":5
         * }
         * ] }
         */
        $modelUserTag->syncTagList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function tagUser($user_to_usertag_id)
    {
        $modelUserToUserTag = new \App\Weixin2\Models\User\UserToUserTag();
        $userToTagInfo = $modelUserToUserTag->getInfoById($user_to_usertag_id);
        if (empty($userToTagInfo)) {
            throw new \Exception("用户和用户标签对应记录ID:{$user_to_usertag_id}所对应的记录不存在");
        }
        $openidList = array();
        $openidList[] = $userToTagInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchtagging($userToTagInfo['tag_id'], $openidList);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelUserToUserTag->tag($user_to_usertag_id, $res, time());
        return $res;
    }

    public function untagUser($user_to_usertag_id)
    {
        $modelUserToUserTag = new \App\Weixin2\Models\User\UserToUserTag();
        $userToTagInfo = $modelUserToUserTag->getInfoById($user_to_usertag_id);
        if (empty($userToTagInfo)) {
            throw new \Exception("用户和用户标签对应记录ID:{$user_to_usertag_id}所对应的记录不存在");
        }
        $openidList = array();
        $openidList[] = $userToTagInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchuntagging($userToTagInfo['tag_id'], $openidList);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUserToUserTag->untag($user_to_usertag_id, time());

        return $res;
    }

    public function blackUser($black_user_id)
    {
        $modelBlackUser = new \App\Weixin2\Models\User\BlackUser();
        $blackUserInfo = $modelBlackUser->getInfoById($black_user_id);
        if (empty($blackUserInfo)) {
            throw new \Exception("黑名单用户对应记录ID:{$black_user_id}所对应的记录不存在");
        }
        $openid_list = array();
        $openid_list[] = $blackUserInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchblacklist($openid_list);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelBlackUser->black($black_user_id, $res, time());
        return $res;
    }

    public function unblackUser($black_user_id)
    {
        $modelBlackUser = new \App\Weixin2\Models\User\BlackUser();
        $blackUserInfo = $modelBlackUser->getInfoById($black_user_id);
        if (empty($blackUserInfo)) {
            throw new \Exception("黑名单用户对应记录ID:{$black_user_id}所对应的记录不存在");
        }
        $openid_list = array();
        $openid_list[] = $blackUserInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchunblacklist($openid_list);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelBlackUser->unblack($black_user_id, time());

        return $res;
    }

    public function syncBlackList()
    {
        $modelBlackUser = new \App\Weixin2\Models\User\BlackUser();
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->getblacklist();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "total":23000,
         * "count":10000,
         * "data":{"
         * openid":[
         * "OPENID1",
         * "OPENID2",
         * ...,
         * "OPENID10000"
         * ]
         * },
         * "next_openid":"OPENID10000"
         * }
         */
        $modelBlackUser->syncBlackList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function sendMassMsg($tag_id, array $toUsers, $massMsgInfo, $sendMethodInfo, $match, $is_send = false)
    {
        $modelMassMsg = new \App\Weixin2\Models\MassMsg\MassMsg();

        $objMassSender = $this->getWeixinObject()
            ->getMsgManager()
            ->getMassSender();

        // 预览用户
        $previewUser = "o2N5jt56GlMdqv46BWxvK0ND-eIw";

        $massmsg = array();

        try {
            $title = empty($massMsgInfo['title']) ? "" : $massMsgInfo['title'];
            $description = empty($massMsgInfo['description']) ? "" : $massMsgInfo['description'];
            $media_id = empty($massMsgInfo['media_id']) ? "" : $massMsgInfo['media_id'];
            $thumb_media_id = empty($massMsgInfo['thumb_media_id']) ? "" : $massMsgInfo['thumb_media_id'];

            // 发送方式
            $is_to_all = !empty($sendMethodInfo['send_method']) ? false : true;
            $send_ignore_reprint = empty($massMsgInfo['send_ignore_reprint']) ? 0 : 1;
            $clientmsgid = empty($sendMethodInfo['clientmsgid']) ? "" : $sendMethodInfo['clientmsgid'];

            // 文本
            if ($massMsgInfo['msg_type'] == "text") {
                if (!$is_send) {
                    $res = $objMassSender->previewText($previewUser, $massMsgInfo['description'], $title, $description);
                } else {
                    if (!empty($tag_id)) {
                        $res = $objMassSender->sendTextByTag($tag_id, $massMsgInfo['description'], $is_to_all, $title, $description, $send_ignore_reprint, $clientmsgid);
                    } else {
                        $res = $objMassSender->sendTextByOpenid($toUsers, $massMsgInfo['description'], $title, $description, $send_ignore_reprint, $clientmsgid);
                    }
                }
            }

            // 语音/音频
            elseif ($massMsgInfo['msg_type'] == "voice" || $massMsgInfo['msg_type'] == "music") {
                // 永久素材没有的话
                if (empty($media_id)) {
                    // 如果有临时素材的进行处理
                    if (!empty($massMsgInfo['media'])) {
                        $media_result = $this->uploadMedia($massMsgInfo['media']);
                        $media_id = $media_result['media_id'];
                    }
                }
                if (!$is_send) {
                    $res = $objMassSender->previewVoice($previewUser, $media_id, $title, $description);
                } else {
                    if (!empty($tag_id)) {
                        $res = $objMassSender->sendVoiceByTag($tag_id, $media_id, $is_to_all, $title, $description, $send_ignore_reprint, $clientmsgid);
                    } else {
                        $res = $objMassSender->sendVoiceByOpenid($toUsers, $media_id, $title, $description, $send_ignore_reprint, $clientmsgid);
                    }
                }
            }

            // 图片
            elseif ($massMsgInfo['msg_type'] == "image") {
                // 永久素材没有的话
                if (empty($media_id)) {
                    // 如果有临时素材的进行处理
                    if (!empty($massMsgInfo['media'])) {
                        $media_result = $this->uploadMedia($massMsgInfo['media']);
                        $media_id = $media_result['media_id'];
                    }
                }
                if (!$is_send) {
                    $res = $objMassSender->previewImage($previewUser, $media_id, $title, $description);
                } else {
                    if (!empty($tag_id)) {
                        $res = $objMassSender->sendImageByTag($tag_id, $media_id, $is_to_all, $title, $description, $send_ignore_reprint, $clientmsgid);
                    } else {
                        $res = $objMassSender->sendImageByOpenid($toUsers, $media_id, $title, $description, $send_ignore_reprint, $clientmsgid);
                    }
                }
            }

            // 视频
            elseif ($massMsgInfo['msg_type'] == "mpvideo") {
                // 永久素材没有的话
                if (empty($media_id)) {
                    // 如果有临时素材的进行处理
                    if (!empty($massMsgInfo['media'])) {
                        $media_result = $this->uploadMedia($massMsgInfo['media']);
                        $media_id = $media_result['media_id'];
                        // 如果没有上传过视频文件的话就调用接口上传
                        $res4UploadVideo = $this->getWeixinObject()
                            ->getMediaManager()
                            ->uploadVideo($media_id, $title, $description);
                        if (!empty($res4UploadVideo['errcode'])) {
                            throw new \Exception($res4UploadVideo['errmsg'] . \json_encode(array(
                                'media_id' => $media_id,
                                'title' => $title,
                                'description' => $description
                            )), $res4UploadVideo['errcode']);
                        }
                        /**
                         * {
                         * "type":"video",
                         * "media_id":"IhdaAQXuvJtGzwwc0abfXnzeezfO0NgPK6AQYShD8RQYMTtfzbLdBIQkQziv2XJc",
                         * "created_at":1398848981
                         * }
                         */
                        $modelMassMsg->recordUploadResult($massMsgInfo['_id'], $res4UploadVideo, time());

                        $modelMassMsg['upload_media_id'] = $res4UploadVideo['media_id'];
                        $modelMassMsg['upload_media_created_at'] = $res4UploadVideo['created_at'];
                        $modelMassMsg['upload_media_type'] = $res4UploadVideo['type'];

                        $media_id = $res4UploadVideo['media_id'];
                    }
                }
                if (!$is_send) {
                    $res = $objMassSender->previewVideo($previewUser, $media_id, $title, $description);
                } else {
                    if (!empty($tag_id)) {
                        $res = $objMassSender->sendVideoByTag($tag_id, $media_id, $is_to_all, $title, $description, $send_ignore_reprint, $clientmsgid);
                    } else {
                        $res = $objMassSender->sendVideoByOpenid($toUsers, $media_id, $title, $description, $send_ignore_reprint, $clientmsgid);
                    }
                }
            }

            // 图文消息
            elseif ($massMsgInfo['msg_type'] == "mpnews") {
                // 如果没有永久的图文素材media_id的话
                if (empty($media_id)) {
                    // 如果没有上传图文的media_id的话

                    // 查找对应的群发图文消息
                    $modelMassMsgNews = new \App\Weixin2\Models\MassMsg\News();
                    $articles = $modelMassMsgNews->getArticlesByMassMsgId($massMsgInfo['mass_msg_id'], $this->authorizer_appid, $this->component_appid, $massMsgInfo['agentid']);
                    if (empty($articles)) {
                        throw new \Exception("群发消息ID:{$massMsgInfo['mass_msg_id']}所对应的图文不存在");
                    }
                    foreach ($articles as &$article) {
                        $res4ThumbMedia = $this->uploadMedia($article['thumb_media']);
                        $article['thumb_media_id'] = $res4ThumbMedia['media_id'];
                        unset($article['thumb_media']);
                    }
                    // 上传图文消息素材（用于群发图文消息）
                    $res4UploadNews = $this->getWeixinObject()
                        ->getMediaManager()
                        ->uploadNews($articles);
                    if (!empty($res4UploadNews['errcode'])) {
                        throw new \Exception($res4UploadNews['errmsg'] . \json_encode($articles), $res4UploadNews['errcode']);
                    }
                    /**
                     * {
                     * "type":"news",
                     * "media_id":"CsEf3ldqkAYJAU6EJeIkStVDSvffUJ54vqbThMgplD-VJXXof6ctX5fI6-aYyUiQ",
                     * "created_at":1391857799
                     * }
                     */
                    $modelMassMsg->recordUploadResult($massMsgInfo['_id'], $res4UploadNews, time());

                    $modelMassMsg['upload_media_id'] = $res4UploadNews['media_id'];
                    $modelMassMsg['upload_media_created_at'] = $res4UploadNews['created_at'];
                    $modelMassMsg['upload_media_type'] = $res4UploadNews['type'];

                    $media_id = $res4UploadNews['media_id'];
                }

                if (!$is_send) {
                    $res = $objMassSender->previewGraphText($previewUser, $media_id, $title, $description);
                } else {
                    if (!empty($tag_id)) {
                        $res = $objMassSender->sendGraphTextByTag($tag_id, $media_id, $is_to_all, $title, $description, $send_ignore_reprint, $clientmsgid);
                    } else {
                        $res = $objMassSender->sendGraphTextByOpenid($toUsers, $media_id, $title, $description, $send_ignore_reprint, $clientmsgid);
                    }
                }
            }

            // 卡券消息
            elseif ($massMsgInfo['msg_type'] == "wxcard") {
                if (!$is_send) {
                    $res = $objMassSender->previewWxcard($previewUser, $massMsgInfo['card_id'], array(), $title, $description);
                } else {
                    if (!empty($tag_id)) {
                        $res = $objMassSender->sendWxcardByTag($tag_id, $massMsgInfo['card_id'], array(), $is_to_all, $title, $description, $send_ignore_reprint, $clientmsgid);
                    } else {
                        $res = $objMassSender->sendWxcardByOpenid($toUsers, $massMsgInfo['card_id'], array(), $title, $description, $send_ignore_reprint, $clientmsgid);
                    }
                }
            } else {
                throw new \Exception("群发消息类型:{$massMsgInfo['msg_type']}不存在");
            }

            if (!empty($res['errcode'])) {
                throw new \Exception($res['errmsg'], $res['errcode']);
            }
            $msg_id = empty($res['msg_id']) ? "" : $res['msg_id'];
            $msg_data_id = empty($res['msg_data_id']) ? "" : $res['msg_data_id'];
            $massmsg = $res;
        } catch (\Exception $e) {
            $massmsg['errorcode'] = $e->getCode();
            $massmsg['errormsg'] = $e->getMessage();
        }

        if (empty($massmsg)) {
            $massmsg = "";
        } else {
            $massmsg = \json_encode($massmsg);
        }

        // 记录日志
        $modelMassMsgSendLog = new \App\Weixin2\Models\MassMsg\SendLog();
        $modelMassMsgSendLog->record($massMsgInfo['component_appid'], $massMsgInfo['authorizer_appid'], $massMsgInfo['agentid'], $massMsgInfo['_id'], $massMsgInfo['name'], $massMsgInfo['msg_type'], $massMsgInfo['media'], $massMsgInfo['media_id'], $massMsgInfo['thumb_media'], $massMsgInfo['thumb_media_id'], $massMsgInfo['title'], $massMsgInfo['description'], $massMsgInfo['card_id'], $massMsgInfo['card_ext'], $massMsgInfo['upload_media_id'], $massMsgInfo['upload_media_created_at'], $massMsgInfo['upload_media_type'], $is_to_all, $tag_id, \json_encode($toUsers), $send_ignore_reprint, $clientmsgid, $match['_id'], $match['keyword'], $match['mass_msg_type'], "", "", $massmsg, $msg_id, $msg_data_id, time());

        return array(
            'is_ok' => true,
            'api_ret' => $massmsg
        );
    }

    public function deleteMassMsg($mass_msg_send_log_id)
    {
        $modelMassMsgSendLog = new \App\Weixin2\Models\MassMsg\SendLog();
        $massMsgSendLogInfo = $modelMassMsgSendLog->getInfoById($mass_msg_send_log_id);
        if (empty($massMsgSendLogInfo)) {
            throw new \Exception("群发消息发送日志记录ID:{$mass_msg_send_log_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getMsgManager()
            ->getMassSender()
            ->delete($massMsgSendLogInfo['msg_id']);
        /**
         * 1、只有已经发送成功的消息才能删除
         * 2、删除消息是将消息的图文详情页失效，已经收到的用户，还是能在其本地看到消息卡片。
         * 3、删除群发消息只能删除图文消息和视频消息，其他类型的消息一经发送，无法删除。
         * 4、如果多次群发发送的是一个图文消息，那么删除其中一次群发，就会删除掉这个图文消息也，导致所有群发都失效
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMassMsgSendLog->removeMsgId($mass_msg_send_log_id);
        return $res;
    }

    public function getMassMsg($mass_msg_send_log_id)
    {
        $modelMassMsgSendLog = new \App\Weixin2\Models\MassMsg\SendLog();
        $massMsgSendLogInfo = $modelMassMsgSendLog->getInfoById($mass_msg_send_log_id);
        if (empty($massMsgSendLogInfo)) {
            throw new \Exception("群发消息发送日志记录ID:{$mass_msg_send_log_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getMsgManager()
            ->getMassSender()
            ->get($massMsgSendLogInfo['msg_id']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMassMsgSendLog->recordMsgStatus($mass_msg_send_log_id, $res);
        return $res;
    }

    public function answerReplyMsgs($FromUserName, $ToUserName, $match)
    {
        $modelReplyMsg = new \App\Weixin2\Models\ReplyMsg\ReplyMsg();
        $replyMsgs = $modelReplyMsg->getReplyMsgsByKeyword($match);
        if (empty($replyMsgs)) {
            return "";
        }

        $objWeixin = $this->getWeixinObject();
        // 设定来源和目标用户的openid
        $objWeixin->setFromAndTo($FromUserName, $ToUserName);

        switch ($match['reply_msg_type']) {
            case 'news':
                $articles = array();
                // 获取图文列表
                $isFirst = empty($articles) ? true : false;
                $modelReplyMsgNews = new \App\Weixin2\Models\ReplyMsg\News();
                $articles1 = $modelReplyMsgNews->getArticlesByReplyMsgId($replyMsgs[0]['_id'], $replyMsgs[0]['authorizer_appid'], $replyMsgs[0]['component_appid'], $replyMsgs[0]['agentid'], $isFirst);
                $articles = array_merge($articles, $articles1);
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyGraphText($articles);
                break;
            case 'music':
                $thumb_media_id = $this->getMediaId4ReplyMsg('thumb', $replyMsgs[0]);
                $hqmusic = empty($replyMsgs[0]['hqmusic']) ? "" : $modelReplyMsg->getPhysicalFilePath($replyMsgs[0]['hqmusic']);
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyMusic($replyMsgs[0]['title'], $replyMsgs[0]['description'], $modelReplyMsg->getPhysicalFilePath($replyMsgs[0]['music']), $hqmusic, $thumb_media_id);
                break;
            case 'text':
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyText($replyMsgs[0]['description']);
                break;
            case 'voice':
                $media_id = $this->getMediaId4ReplyMsg('voice', $replyMsgs[0]);
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyVoice($media_id);
                break;
            case 'video':
                $media_id = $this->getMediaId4ReplyMsg('video', $replyMsgs[0]);
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyVideo($replyMsgs[0]['title'], $replyMsgs[0]['description'], $media_id);
                break;
            case 'image':
                $media_id = $this->getMediaId4ReplyMsg('image', $replyMsgs[0]);
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyImage($media_id);
                break;
            case 'transfer_customer_service':
                $kf_account = empty($replyMsgs[0]['kf_account']) ? "" : $replyMsgs[0]['kf_account'];
                $replymsg = $objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyCustomerService($kf_account);
                break;
        }

        // 记录日志
        $modelReplyMsgSendLog = new \App\Weixin2\Models\ReplyMsg\SendLog();
        $modelReplyMsgSendLog->record($replyMsgs[0]['component_appid'], $replyMsgs[0]['authorizer_appid'], $replyMsgs[0]['agentid'], $replyMsgs[0]['_id'], $replyMsgs[0]['name'], $replyMsgs[0]['msg_type'], $replyMsgs[0]['media'], $replyMsgs[0]['media_id'], $replyMsgs[0]['thumb_media'], $replyMsgs[0]['thumb_media_id'], $replyMsgs[0]['title'], $replyMsgs[0]['description'], $replyMsgs[0]['music'], $replyMsgs[0]['hqmusic'], $replyMsgs[0]['kf_account'], $match['_id'], $match['keyword'], $match['reply_msg_type'], $ToUserName, $FromUserName, $replymsg, time());

        return $replymsg;
    }

    public function answerCustomMsgs($FromUserName, $ToUserName, $match)
    {
        $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
        $customMsgs = $modelCustomMsg->getCustomMsgsByKeyword($match);
        if (empty($customMsgs)) {
            return false;
        }

        $sendRet = $this->sendCustomMsg($FromUserName, $ToUserName, $customMsgs[0], $match);
        return $sendRet['is_ok'];
    }

    public function sendCustomMsg($FromUserName, $ToUserName, $customMsgInfo, $match)
    {
        $objWeixin = $this->getWeixinObject();
        $custommsg = array();

        try {
            switch ($match['custom_msg_type']) {
                case 'news':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelCustomMsgNews = new \App\Weixin2\Models\CustomMsg\News();
                    $articles1 = $modelCustomMsgNews->getArticlesByCustomMsgId($customMsgInfo['_id'], $customMsgInfo['authorizer_appid'], $customMsgInfo['component_appid'], $customMsgInfo['agentid'], $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendGraphText($FromUserName, $articles);
                    break;
                case 'music':
                    $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $thumb_media_id = $this->getMediaId4CustomMsg('thumb', $customMsgInfo);
                    $hqmusic = empty($customMsgInfo['hqmusic']) ? "" : $modelCustomMsg->getPhysicalFilePath($customMsgInfo['hqmusic']);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMusic($FromUserName, $customMsgInfo['title'], $customMsgInfo['description'], $modelCustomMsg->getPhysicalFilePath($customMsgInfo['music']), $hqmusic, $thumb_media_id);
                    break;
                case 'text':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendText($FromUserName, $customMsgInfo['description']);
                    break;
                case 'voice':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('voice', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendVoice($FromUserName, $media_id);
                    break;
                case 'video':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('video', $customMsgInfo);
                    $thumb_media_id = $this->getMediaId4CustomMsg('thumb', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendVideo($FromUserName, $media_id, $thumb_media_id, $customMsgInfo['title'], $customMsgInfo['description']);
                    break;
                case 'image':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('image', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendImage($FromUserName, $media_id);
                    break;
                case 'mpnews':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('mpnews', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMpNews($FromUserName, $media_id);
                    break;
                case 'msgmenu':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $msgmenu = empty($customMsgInfo['description']) ? array() : \json_decode($customMsgInfo['description'], true);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMsgMenu($FromUserName, $msgmenu);
                    break;
                case 'miniprogrampage':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $thumb_media_id = $this->getMediaId4CustomMsg('thumb', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMiniProgramPage($FromUserName, $customMsgInfo['title'], $customMsgInfo['appid'], $customMsgInfo['pagepath'], $thumb_media_id);
                    break;
                case 'wxcard':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $card_ext = empty($customMsgInfo['card_ext']) ? array() : \json_decode($customMsgInfo['card_ext'], true);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendWxcard($FromUserName, $customMsgInfo['card_id'], $card_ext);
                    break;
            }

            if (!empty($custommsg['errcode'])) {
                throw new \Exception($custommsg['errmsg'], $custommsg['errcode']);
            }
        } catch (\Exception $e) {
            $custommsg['errorcode'] = $e->getCode();
            $custommsg['errormsg'] = $e->getMessage();
        }

        if (empty($custommsg)) {
            $custommsg = "";
        } else {
            $custommsg = \json_encode($custommsg);
        }
        // 记录日志
        $modelCustomMsgSendLog = new \App\Weixin2\Models\CustomMsg\SendLog();
        $modelCustomMsgSendLog->record($customMsgInfo['component_appid'], $customMsgInfo['authorizer_appid'], $customMsgInfo['agentid'], $customMsgInfo['_id'], $customMsgInfo['name'], $customMsgInfo['msg_type'], $customMsgInfo['media'], $customMsgInfo['media_id'], $customMsgInfo['thumb_media'], $customMsgInfo['thumb_media_id'], $customMsgInfo['title'], $customMsgInfo['description'], $customMsgInfo['music'], $customMsgInfo['hqmusic'], $customMsgInfo['appid'], $customMsgInfo['pagepath'], $customMsgInfo['card_id'], $customMsgInfo['card_ext'], $customMsgInfo['kf_account'], $match['_id'], $match['keyword'], $match['custom_msg_type'], $ToUserName, $FromUserName, $custommsg, time());

        return array(
            'is_ok' => true,
            'api_ret' => $custommsg
        );
    }

    public function addOrUpdateKfAccount($kfaccount_id, $is_add = true)
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $kfAccountInfo = $modelKfAccount->getInfoById($kfaccount_id);
        if (empty($kfAccountInfo)) {
            throw new \Exception("客服帐号记录ID:{$kfaccount_id}所对应的记录不存在");
        }
        if ($is_add) {
            $method = "kfaccountAdd";
        } else {
            $method = "kfaccountUpdate";
        }

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->$method($kfAccountInfo['kf_account'], $kfAccountInfo['nickname'], $kfAccountInfo['password']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelKfAccount->updateCreatedStatus($kfaccount_id, $res, time());
        return $res;
    }

    public function deleteKfAccount($kfaccount_id)
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $kfAccountInfo = $modelKfAccount->getInfoById($kfaccount_id);
        if (empty($kfAccountInfo)) {
            throw new \Exception("客服帐号记录ID:{$kfaccount_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->kfaccountDel($kfAccountInfo['kf_account']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelKfAccount->removeCreatedStatus($kfaccount_id, time());

        return $res;
    }

    public function syncKfAccountList()
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->getkflist();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "kf_list": [
         * {
         * "kf_account": "test1@test",
         * "kf_nick": "ntest1",
         * "kf_id": "1001"，
         * "kf_headimgurl": " http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw/0"
         * },
         * {
         * "kf_account": "test2@test",
         * "kf_nick": "ntest2",
         * "kf_id": "1002"，
         * "kf_headimgurl": " http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw /0"
         * },
         * {
         * "kf_account": "test3@test",
         * "kf_nick": "ntest3",
         * "kf_id": "1003"，
         * "kf_headimgurl": " http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw /0"
         * }
         * ]
         * }
         */
        $modelKfAccount->syncKfAccountList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncOnlineKfAccountList()
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->getonlinekflist();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "kf_online_list" : [
         * {
         * "kf_account" :
         * "test1@test" ,
         * "status" : 1,
         * "kf_id" :
         * "1001" ,
         * "accepted_case" : 1
         * },
         * {
         * "kf_account" :
         * "test2@test" ,
         * "status" : 1,
         * "kf_id" :
         * "1002" ,
         * "accepted_case" : 2
         * }
         * ]
         * }
         */
        $modelKfAccount->syncOnlineKfAccountList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function inviteWorker($kfaccount_id)
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $kfAccountInfo = $modelKfAccount->getInfoById($kfaccount_id);
        if (empty($kfAccountInfo)) {
            throw new \Exception("客服帐号记录ID:{$kfaccount_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->inviteWorker($kfAccountInfo['kf_account'], $kfAccountInfo['invite_wx']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        return $res;
    }

    public function deleteTemplate($template_rec_id)
    {
        $modelTemplate = new \App\Weixin2\Models\Template\Template();
        $templateInfo = $modelTemplate->getInfoById($template_rec_id);
        if (empty($templateInfo)) {
            throw new \Exception("模板记录ID:{$template_rec_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getMsgManager()
            ->getTemplateSender()
            ->delPrivateTemplate($templateInfo['template_id']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $templateInfo->removeCreatedStatus($template_rec_id, time());

        return $res;
    }

    public function syncTemplateList()
    {
        $modelTemplate = new \App\Weixin2\Models\Template\Template();
        $res = $this->getWeixinObject()
            ->getMsgManager()
            ->getTemplateSender()
            ->getAllPrivateTemplate();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "template_list": [{
         * "template_id": "iPk5sOIt5X_flOVKn5GrTFpncEYTojx6ddbt8WYoV5s",
         * "title": "领取奖金提醒",
         * "primary_industry": "IT科技",
         * "deputy_industry": "互联网|电子商务",
         * "content": "{ {result.DATA} }\n\n领奖金额:{ {withdrawMoney.DATA} }\n领奖 时间: { {withdrawTime.DATA} }\n银行信息:{ {cardInfo.DATA} }\n到账时间: { {arrivedTime.DATA} }\n{ {remark.DATA} }",
         * "example": "您已提交领奖申请\n\n领奖金额：xxxx元\n领奖时间：2013-10-10 12:22:22\n银行信息：xx银行(尾号xxxx)\n到账时间：预计xxxxxxx\n\n预计将于xxxx到达您的银行卡"
         * }]
         * }
         */
        $modelTemplate->syncTemplateList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function answerTemplateMsgs($FromUserName, $ToUserName, $match)
    {
        $modelTemplateMsg = new \App\Weixin2\Models\TemplateMsg\TemplateMsg();
        $templates = $modelTemplateMsg->getTemplateMsgsByKeyword($match);
        if (empty($templates)) {
            return false;
        }
        $sendRet = $this->sendTemplateMsg($FromUserName, $ToUserName, $templates[0], $match);
        return $sendRet['is_ok'];
    }

    public function sendTemplateMsg($FromUserName, $ToUserName, $templateMsgInfo, $match)
    {
        $objWeixin = $this->getWeixinObject();
        $templatemsg = array();

        try {
            $data = empty($templateMsgInfo['data']) ? array() : \json_decode($templateMsgInfo['data'], true);
            $appid = empty($templateMsgInfo['appid']) ? "" : $templateMsgInfo['appid'];
            $pagepath = empty($templateMsgInfo['pagepath']) ? "" : $templateMsgInfo['pagepath'];
            $miniprogram = NULL;
            if (!empty($appid)) {
                $miniprogram['appid'] = $appid;
            }
            if (!empty($pagepath)) {
                $miniprogram['pagepath'] = $pagepath;
            }
            $templatemsg = $objWeixin->getMsgManager()
                ->getTemplateSender()
                ->send($FromUserName, $templateMsgInfo['template_id'], $templateMsgInfo['url'], $templateMsgInfo['color'], $data, $miniprogram);

            if (!empty($templatemsg['errcode'])) {
                throw new \Exception($templatemsg['errmsg'], $templatemsg['errcode']);
            }
        } catch (\Exception $e) {
            $templatemsg['errorcode'] = $e->getCode();
            $templatemsg['errormsg'] = $e->getMessage();
        }

        if (empty($templatemsg)) {
            $templatemsg = "";
        } else {
            $templatemsg = \json_encode($templatemsg);
        }

        // 记录日志
        $modelTemplateMsgSendLog = new \App\Weixin2\Models\TemplateMsg\SendLog();
        $modelTemplateMsgSendLog->record($templateMsgInfo['component_appid'], $templateMsgInfo['authorizer_appid'], $templateMsgInfo['agentid'], $templateMsgInfo['_id'], $templateMsgInfo['name'], $templateMsgInfo['template_id'], $templateMsgInfo['url'], $templateMsgInfo['data'], $templateMsgInfo['color'], $templateMsgInfo['appid'], $templateMsgInfo['pagepath'], $match['_id'], $match['keyword'], $ToUserName, $FromUserName, $templatemsg, time());

        return array(
            'is_ok' => true,
            'api_ret' => $templatemsg
        );
    }

    public function shorturl($shorturl_id)
    {
        $modelShorturl = new \App\Weixin2\Models\Shorturl();
        $shorturlInfo = $modelShorturl->getInfoById($shorturl_id);
        if (empty($shorturlInfo)) {
            throw new \Exception("短连接记录ID:{$shorturl_id}所对应的记录不存在");
        }
        $action = $shorturlInfo['action'];
        $res = $this->getWeixinObject()
            ->getShortUrlManager()
            ->$action($shorturlInfo['long_url']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelShorturl->updateCreatedStatus($shorturl_id, $res, time());
        return $res;
    }

    public function createKfSession($kfsession_id)
    {
        $modelKfSession = new \App\Weixin2\Models\Kf\Session();
        $kfSessionInfo = $modelKfSession->getInfoById($kfsession_id);
        if (empty($kfSessionInfo)) {
            throw new \Exception("客服会话记录ID:{$kfsession_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->kfsessionCreate($kfSessionInfo['kf_account'], $kfSessionInfo['openid']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelKfSession->updateCreatedStatus($kfsession_id, $res, time());
        return $res;
    }

    public function closeKfSession($kfsession_id)
    {
        $modelKfSession = new \App\Weixin2\Models\Kf\Session();
        $kfSessionInfo = $modelKfSession->getInfoById($kfsession_id);
        if (empty($kfSessionInfo)) {
            throw new \Exception("客服会话记录ID:{$kfsession_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->kfsessionClose($kfSessionInfo['kf_account'], $kfSessionInfo['openid']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelKfSession->removeCreatedStatus($kfsession_id, time());

        return $res;
    }

    public function syncMsgRecordList($msgrecord_start_time, $msgrecord_end_time)
    {
        $modelMsgRecord = new \App\Weixin2\Models\Kf\MsgRecord();
        $starttime = strtotime(date('Y-m-d', $msgrecord_start_time) . " 00:00:00");
        $endtime = strtotime(date('Y-m-d', $msgrecord_end_time) . " 23:59:59");

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->getMsgList($starttime, $endtime);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "recordlist" : [
         * {
         * "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk" ,
         * "opercode" : 2002,
         * "text" : " 您好，客服test1为您服务。" ,
         * "time" : 1400563710,
         * "worker" : "test1@test"
         * },
         * {
         * "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk" ,
         * "opercode" : 2003,
         * "text" : "你好，有什么事情？" ,
         * "time" : 1400563731,
         * "worker" : "test1@test"
         * }
         * ],
         * "number":2,
         * "msgid":20165267
         * }
         */
        $modelMsgRecord->syncMsgRecordList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserSummary($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserSummary = new \App\Weixin2\Models\DataCube\UserSummary();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserSummary($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "user_source": 0,
         * "new_user": 0,
         * "cancel_user": 0
         * }//后续还有ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserSummary->syncUserSummary($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserCumulate($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserCumulate = new \App\Weixin2\Models\DataCube\UserCumulate();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserCumulate($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "cumulate_user": 1217056
         * }, //后续还有ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserCumulate->syncUserCumulate($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncInterfaceSummary($start_ref_date, $end_ref_date)
    {
        $modelDataCubeInterfaceSummary = new \App\Weixin2\Models\DataCube\InterfaceSummary();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getInterfaceSummary($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "callback_count": 36974,
         * "fail_count": 67,
         * "total_time_cost": 14994291,
         * "max_time_cost": 5044
         * }//后续还有不同ref_date（在begin_date和end_date之间）的数据
         * ]
         * }
         */
        $modelDataCubeInterfaceSummary->syncInterfaceSummary($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncInterfaceSummaryHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeInterfaceSummaryHour = new \App\Weixin2\Models\DataCube\InterfaceSummaryHour();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getInterfaceSummaryHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-01",
         * "ref_hour": 0,
         * "callback_count": 331,
         * "fail_count": 18,
         * "total_time_cost": 167870,
         * "max_time_cost": 5042
         * }//后续还有不同ref_hour的数据
         * ]
         * }
         */
        $modelDataCubeInterfaceSummaryHour->syncInterfaceSummaryHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsg($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsg = new \App\Weixin2\Models\DataCube\UpstreamMsg();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsg($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "msg_type": 1,
         * "msg_user": 282,
         * "msg_count": 817
         * }//后续还有同一ref_date的不同msg_type的数据，以及不同ref_date（在时间范围内）的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsg->syncUpstreamMsg($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgHour = new \App\Weixin2\Models\DataCube\UpstreamMsgHour();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "ref_hour": 0,
         * "msg_type": 1,
         * "msg_user": 9,
         * "msg_count": 10
         * }//后续还有同一ref_hour的不同msg_type的数据，以及不同ref_hour的数据，ref_date固定，因为最大时间跨度为1
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgHour->syncUpstreamMsgHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgWeek($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgWeek = new \App\Weixin2\Models\DataCube\UpstreamMsgWeek();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgWeek($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-08",
         * "msg_type": 1,
         * "msg_user": 16,
         * "msg_count": 27
         * } //后续还有同一ref_date下不同msg_type的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgWeek->syncUpstreamMsgWeek($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgMonth($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgMonth = new \App\Weixin2\Models\DataCube\UpstreamMsgMonth();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgMonth($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-11-01",
         * "msg_type": 1,
         * "msg_user": 7989,
         * "msg_count": 42206
         * }//后续还有同一ref_date下不同msg_type的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgMonth->syncUpstreamMsgMonth($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDist($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDist = new \App\Weixin2\Models\DataCube\UpstreamMsgDist();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDist($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDist->syncUpstreamMsgDist($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDistHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDistHour = new \App\Weixin2\Models\DataCube\UpstreamMsgDistHour();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDistHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDistHour->syncUpstreamMsgDistHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDistWeek($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDistWeek = new \App\Weixin2\Models\DataCube\UpstreamMsgDistWeek();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDistWeek($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDistWeek->syncUpstreamMsgDistWeek($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDistMonth($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDistMonth = new \App\Weixin2\Models\DataCube\UpstreamMsgDistMonth();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDistMonth($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDistMonth->syncUpstreamMsgDistMonth($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncArticleSummary($start_ref_date, $end_ref_date)
    {
        $modelDataCubeArticleSummary = new \App\Weixin2\Models\DataCube\ArticleSummary();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getArticleSummary($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-08",
         * "msgid": "10000050_1",
         * "title": "12月27日 DiLi日报",
         * "int_page_read_user": 23676,
         * "int_page_read_count": 25615,
         * "ori_page_read_user": 29,
         * "ori_page_read_count": 34,
         * "share_user": 122,
         * "share_count": 994,
         * "add_to_fav_user": 1,
         * "add_to_fav_count": 3
         * }
         * //后续会列出该日期内所有被阅读过的文章（仅包括群发的文章）在当天的阅读次数等数据
         * ]
         * }
         */
        $modelDataCubeArticleSummary->syncArticleSummary($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncArticleTotal($start_ref_date, $end_ref_date)
    {
        $modelDataCubeArticleTotal = new \App\Weixin2\Models\DataCube\ArticleTotal();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);
        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getArticleTotal($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-14",
         * "msgid": "202457380_1",
         * "title": "马航丢画记",
         * "details": [
         * {
         * "stat_date": "2014-12-14",
         * "target_user": 261917,
         * "int_page_read_user": 23676,
         * "int_page_read_count": 25615,
         * "ori_page_read_user": 29,
         * "ori_page_read_count": 34,
         * "share_user": 122,
         * "share_count": 994,
         * "add_to_fav_user": 1,
         * "add_to_fav_count": 3,
         * "int_page_from_session_read_user": 657283,
         * "int_page_from_session_read_count": 753486,
         * "int_page_from_hist_msg_read_user": 1669,
         * "int_page_from_hist_msg_read_count": 1920,
         * "int_page_from_feed_read_user": 367308,
         * "int_page_from_feed_read_count": 433422,
         * "int_page_from_friends_read_user": 15428,
         * "int_page_from_friends_read_count": 19645,
         * "int_page_from_other_read_user": 477,
         * "int_page_from_other_read_count": 703,
         * "feed_share_from_session_user": 63925,
         * "feed_share_from_session_cnt": 66489,
         * "feed_share_from_feed_user": 18249,
         * "feed_share_from_feed_cnt": 19319,
         * "feed_share_from_other_user": 731,
         * "feed_share_from_other_cnt": 775
         * }, //后续还会列出所有stat_date符合“ref_date（群发的日期）到接口调用日期”（但最多只统计7天）的数据
         * ]
         * },//后续还有ref_date（群发的日期）在begin_date和end_date之间的群发文章的数据
         * ]
         * }
         */
        $modelDataCubeArticleTotal->syncArticleTotal($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserRead($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserRead = new \App\Weixin2\Models\DataCube\UserRead();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserRead($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "int_page_read_user": 45524,
         * "int_page_read_count": 48796,
         * "ori_page_read_user": 11,
         * "ori_page_read_count": 35,
         * "share_user": 11,
         * "share_count": 276,
         * "add_to_fav_user": 5,
         * "add_to_fav_count": 15
         * }, //后续还有ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserRead->syncUserRead($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserReadHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserReadHour = new \App\Weixin2\Models\DataCube\UserReadHour();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserReadHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * {
         * "list": [
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 0,
         * "int_page_read_user": 6391,
         * "int_page_read_count": 7836,
         * "ori_page_read_user": 375,
         * "ori_page_read_count": 440,
         * "share_user": 2,
         * "share_count": 2,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * },
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 1,
         * "int_page_read_user": 1,
         * "int_page_read_count": 1,
         * "ori_page_read_user": 0,
         * "ori_page_read_count": 0,
         * "share_user": 0,
         * "share_count": 0,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * },
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 2,
         * "int_page_read_user": 3,
         * "int_page_read_count": 3,
         * "ori_page_read_user": 0,
         * "ori_page_read_count": 0,
         * "share_user": 0,
         * "share_count": 0,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * },
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 4,
         * "int_page_read_user": 42,
         * "int_page_read_count": 100,
         * "ori_page_read_user": 0,
         * "ori_page_read_count": 0,
         * "share_user": 0,
         * "share_count": 0,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * }
         * //后续还有ref_hour逐渐增大,以列举1天24小时的数据
         * ]
         * }
         */
        $modelDataCubeUserReadHour->syncUserReadHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserShare($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserShare = new \App\Weixin2\Models\DataCube\UserShare();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserShare($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "share_scene": 1,
         * "share_count": 207,
         * "share_user": 11
         * },
         * {
         * "ref_date": "2014-12-07",
         * "share_scene": 5,
         * "share_count": 23,
         * "share_user": 11
         * }//后续还有不同share_scene（分享场景）的数据，以及ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserShare->syncUserShare($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserShareHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserShareHour = new \App\Weixin2\Models\DataCube\UserShareHour();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserShareHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "ref_hour": 1200,
         * "share_scene": 1,
         * "share_count": 72,
         * "share_user": 4
         * }//后续还有不同share_scene的数据，以及ref_hour逐渐增大的数据。由于最大时间跨度为1，所以ref_date此处固定
         * ]
         * }
         */
        $modelDataCubeUserShareHour->syncUserShareHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function openComment($comment_id)
    {
        $modelComment = new \App\Weixin2\Models\Comment\Comment();
        $commentInfo = $modelComment->getInfoById($comment_id);
        if (empty($commentInfo)) {
            throw new \Exception("已群发文章评论记录ID:{$comment_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->open($commentInfo['user_comment_id'], $commentInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelComment->open($comment_id, $res, time());
        return $res;
    }

    public function closeComment($comment_id)
    {
        $modelComment = new \App\Weixin2\Models\Comment\Comment();
        $commentInfo = $modelComment->getInfoById($comment_id);
        if (empty($commentInfo)) {
            throw new \Exception("已群发文章评论记录ID:{$comment_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->close($commentInfo['user_comment_id'], $commentInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelComment->close($comment_id, $res, time());
        return $res;
    }

    public function syncCommentList($comment_id)
    {
        $modelComment = new \App\Weixin2\Models\Comment\Comment();
        $commentInfo = $modelComment->getInfoById($comment_id);
        if (empty($commentInfo)) {
            throw new \Exception("已群发文章评论记录ID:{$comment_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->getlist($commentInfo['user_comment_id'], $commentInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * “errcode”: 0,
         * “errmsg” : “ok”,
         * “total”: TOTAL //总数，非comment的size around
         * “comment”: [{
         * “user_comment_id” : USER_COMMENT_ID //用户评论id
         * “openid “: OPENID //openid
         * “create_time “: CREATE_TIME //评论时间
         * “content” : CONTENT //评论内容
         * “comment_type “: IS_ELECTED //是否精选评论，0为即非精选，1为true，即精选
         * “reply “: {
         * “content “: CONTENT //作者回复内容
         * “create_time” : CREATE_TIME //作者回复时间
         * }
         * }]
         * }
         */
        $now = time();
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $modelCommentLog->syncCommentList($commentInfo['authorizer_appid'], $commentInfo['component_appid'], $commentInfo['msg_data_id'], $commentInfo['index'], $res, $now);

        $modelCommentReplyLog = new \App\Weixin2\Models\Comment\ReplyLog();
        $modelCommentReplyLog->syncReplyList($commentInfo['authorizer_appid'], $commentInfo['component_appid'], $commentInfo['msg_data_id'], $commentInfo['index'], $res, $now);

        return $res;
    }

    public function markelectComment($comment_log_id)
    {
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $commentLogInfo = $modelCommentLog->getInfoById($comment_log_id);
        if (empty($commentLogInfo)) {
            throw new \Exception("已群发文章评论日志记录ID:{$comment_log_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->markelect($commentLogInfo['user_comment_id'], $commentLogInfo['msg_data_id'], $commentLogInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentLog->markelect($comment_log_id, $res, time());
        return $res;
    }

    public function unmarkelectComment($comment_log_id)
    {
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $commentLogInfo = $modelCommentLog->getInfoById($comment_log_id);
        if (empty($commentLogInfo)) {
            throw new \Exception("已群发文章评论日志记录ID:{$comment_log_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->unmarkelect($commentLogInfo['user_comment_id'], $commentLogInfo['msg_data_id'], $commentLogInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentLog->unmarkelect($comment_log_id, $res, time());
        return $res;
    }

    public function deleteCommentLog($comment_log_id)
    {
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $commentLogInfo = $modelCommentLog->getInfoById($comment_log_id);
        if (empty($commentLogInfo)) {
            throw new \Exception("已群发文章评论日志记录ID:{$comment_log_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->delete($commentLogInfo['user_comment_id'], $commentLogInfo['msg_data_id'], $commentLogInfo['index']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentLog->removeCreateStatus($comment_log_id, $res, time());

        return $res;
    }

    public function addCommentReply($comment_reply_log_id)
    {
        $modelCommentReplyLog = new \App\Weixin2\Models\Comment\ReplyLog();
        $commentReplyLogInfo = $modelCommentReplyLog->getInfoById($comment_reply_log_id);
        if (empty($commentReplyLogInfo)) {
            throw new \Exception("已群发文章评论回复日志记录ID:{$comment_reply_log_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->replyAdd($commentReplyLogInfo['user_comment_id'], $commentReplyLogInfo['content'], $commentReplyLogInfo['msg_data_id'], $commentReplyLogInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentReplyLog->recordCreateStatus($comment_reply_log_id, $res, time());
        return $res;
    }

    public function deleteCommentReply($comment_reply_log_id)
    {
        $modelCommentReplyLog = new \App\Weixin2\Models\Comment\ReplyLog();
        $commentReplyLogInfo = $modelCommentReplyLog->getInfoById($comment_reply_log_id);
        if (empty($commentReplyLogInfo)) {
            throw new \Exception("已群发文章评论回复日志记录ID:{$comment_reply_log_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->replyDelete($commentReplyLogInfo['user_comment_id'], $commentReplyLogInfo['msg_data_id'], $commentReplyLogInfo['index']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentReplyLog->removeCreateStatus($comment_reply_log_id, $res, time());

        return $res;
    }

    public function syncSubscribeUserList($authorizer_appid, $component_appid, $now, $openid4Test)
    {
        $modelSubscribeUser = new \App\Weixin2\Models\User\SubscribeUser();
        $userManager = $this->getWeixinObject()->getUserManager();

        // 先确认accesstoken没有问题
        $FromUserName = $openid4Test;
        $userInfo = $userManager->getUserInfo($FromUserName);
        if (!empty($userInfo['errcode'])) {
            throw new \Exception($userInfo['errmsg'], $userInfo['errcode']);
        }

        // 清空数据
        $query = array(
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        );
        $this->modelSubscribeUser->physicalRemove($query);

        // 参数 说明
        $total = 0; // 关注该公众账号的总用户数
        $count = 0; // 拉取的OPENID个数，最大值为10000
        $data = array(); // 列表数据，OPENID的列表
        $next_openid = ""; // 拉取列表的最后一个用户的OPENID
        for ($i = 1; $i > 0;) {

            $ret = $userManager->getUser($next_openid);
            if (!empty($ret['errcode'])) {
                throw new \Exception($ret['errmsg'], $ret['errcode']);
            }

            $total = $ret['total']; // 关注该公众账号的总用户数
            $count = $count + $ret['count']; // 拉取的OPENID个数，最大值为10000
            $data = empty($ret['data']) ? array() : $ret['data']['openid']; // 列表数据，OPENID的列表
            $next_openid = $ret['next_openid']; // 拉取列表的最后一个用户的OPENID

            if (!empty($data)) {
                foreach ($data as  $openid) {
                    $modelSubscribeUser->log($authorizer_appid, $component_appid, $openid, $now);
                }
            }

            // 按以下的检查判断是否要退出循环
            if (empty($data)) {
                break;
            }

            if (empty($next_openid)) {
                break;
            }

            if ($count >= $total) {
                break;
            }
        }
    }

    private function getMediaId4ReplyMsg($type, $reply)
    {
        if ($type == 'thumb') {
            // 如果永久素材media_id存在的话直接返回
            if (!empty($reply['thumb_media_id'])) {
                return $reply['thumb_media_id'];
            }
            if (!empty($reply['thumb_media'])) {
                $media_result = $this->uploadMedia($reply['thumb_media']);
                return $media_result['media_id'];
            } else {
                throw new \Exception("未指定临时素材", 99999);
            }
        } else {
            // 如果永久素材media_id存在的话直接返回
            if (!empty($reply['media_id'])) {
                return $reply['media_id'];
            }
            if (!empty($reply['media'])) {
                $media_result = $this->uploadMedia($reply['media']);
                return $media_result['media_id'];
            } else {
                throw new \Exception("未指定临时素材", 99999);
            }
        }
    }

    private function getMediaId4CustomMsg($type, $custom)
    {
        if ($type == 'thumb') {
            // 如果永久素材media_id存在的话直接返回
            if (!empty($custom['thumb_media_id'])) {
                return $custom['thumb_media_id'];
            }
            if (!empty($custom['thumb_media'])) {
                $media_result = $this->uploadMedia($custom['thumb_media']);
                return $media_result['media_id'];
            } else {
                throw new \Exception("未指定临时素材", 99999);
            }
        } else {
            // 如果永久素材media_id存在的话直接返回
            if (!empty($custom['media_id'])) {
                return $custom['media_id'];
            }
            if (!empty($custom['media'])) {
                $media_result = $this->uploadMedia($custom['media']);
                return $media_result['media_id'];
            } else {
                throw new \Exception("未指定临时素材", 99999);
            }
        }
    }
}
