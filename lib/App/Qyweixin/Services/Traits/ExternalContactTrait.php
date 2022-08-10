<?php

namespace App\Qyweixin\Services\Traits;

trait ExternalContactTrait
{

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
            $msgTemplateInfo['_id'],
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
            \App\Common\Utils\Helper::myJsonEncode($msg_template_content),
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
        $now = time();
        $modelFollowUser->clearExist($this->authorizer_appid, $this->provider_appid, $now);
        $modelFollowUser->syncFollowUserList($this->authorizer_appid, $this->provider_appid, $res, $now);
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
        $cursor = "";
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->get($external_userid, $cursor);
        if (!empty($res['errcode'])) {
            // {"errcode":84061,"errmsg":"not external contact, hint: [1646594702057801817545474], from ip: 115.29.169.68, more info at https://open.work.weixin.qq.com/devtool/query?e=84061","follow_user":[]}
            if ($res['errcode'] == 84061) {
            } else {
                throw new \Exception($res['errmsg'], $res['errcode']);
            }
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
        $now = time();
        $modelExternalUser->updateExternalUserInfoByApi($userInfo, $res, $now);

        // 同步follow_user
        $modelExternalUserFollowUser = new \App\Qyweixin\Models\ExternalContact\ExternalUserFollowUser();
        $modelExternalUserFollowUser->clearExist($external_userid, $this->authorizer_appid, $this->provider_appid, $now);

        if (!empty($res['follow_user'])) {
            $modelExternalUserFollowUser->syncFollowUserList($external_userid, $this->authorizer_appid, $this->provider_appid, $res, $now);
        }

        if (!empty($res['next_cursor'])) {
            do {
                $cursor = $res['next_cursor'];
                $res = $this->getQyWeixinObject()
                    ->getExternalContactManager()
                    ->get($external_userid, $cursor);
                if (!empty($res['errcode'])) {
                    throw new \Exception($res['errmsg'], $res['errcode']);
                }
                if (!empty($res['follow_user'])) {
                    $modelExternalUserFollowUser->syncFollowUserList($external_userid, $this->authorizer_appid, $this->provider_appid, $res, $now);
                }
                if (empty($res['next_cursor'])) {
                    break;
                }
            } while ($res['next_cursor']);
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
    public function getCorpTagList($tag_id = array(), $group_id = array())
    {
        $modelCorpTag = new \App\Qyweixin\Models\ExternalContact\CorpTag();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getCorpTagList($tag_id, $group_id);
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
        $now = time();
        if (empty($tag_id) && empty($group_id)) {
            $modelCorpTag->clearExist($this->authorizer_appid, $this->provider_appid, $now);
        }
        $modelCorpTag->syncCorpTagList($this->authorizer_appid, $this->provider_appid, $res, $now);
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
    public function getGroupChatList($status_filter = 0, $owner_filter = array(), $cursor = '', $limit = 1000)
    {
        $modelGroupChat = new \App\Qyweixin\Models\ExternalContact\GroupChat();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->getGroupchatList($status_filter, $owner_filter, $cursor, $limit);
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
        $now = time();
        $modelGroupChat->syncGroupChatList($this->authorizer_appid, $this->provider_appid, $res, $now);
        if (!empty($res['next_cursor'])) {
            do {
                $cursor = $res['next_cursor'];
                $res = $this->getQyWeixinObject()
                    ->getExternalContactManager()
                    ->getGroupChatManager()
                    ->getGroupchatList($status_filter, $owner_filter, $cursor, $limit);
                if (!empty($res['errcode'])) {
                    throw new \Exception($res['errmsg'], $res['errcode']);
                }
                $modelGroupChat->syncGroupChatList($this->authorizer_appid, $this->provider_appid, $res, $now);
                if (empty($res['next_cursor'])) {
                    break;
                }
            } while ($res['next_cursor']);
        }
        return $res;
    }

    //获取客户群详情
    public function getGroupChatInfo($groupChatInfo, $need_name = 1)
    {
        $modelGroupChat = new \App\Qyweixin\Models\ExternalContact\GroupChat();
        $chatid = $groupChatInfo['chat_id'];

        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->get($chatid, $need_name);
        if (!empty($res['errcode'])) {
            //chatid不存在
            if ($res['errcode'] == 40050) {
            } else {
                throw new \Exception($res['errmsg'], $res['errcode']);
            }
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
        $now = time();
        $modelGroupChat->updateGroupChatInfoByApi($groupChatInfo, $res, $now);

        // 同步member_list
        $modelGroupChatMember = new \App\Qyweixin\Models\ExternalContact\GroupChatMember();
        $modelGroupChatMember->clearExist($chatid, $this->authorizer_appid, $this->provider_appid, $now);
        if (!empty($res['group_chat']['member_list'])) {
            $modelGroupChatMember->syncMemberList($chatid, $this->authorizer_appid, $this->provider_appid, $res, $now);
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
    public function getGroupChatStatistic($day_begin_time, $day_end_time, array $owner_filter, $order_by = 1, $order_asc = 0, $offset = 0, $limit = 1000)
    {
        $modelGroupChatStatisticByUserid = new \App\Qyweixin\Models\ExternalContact\GroupChatStatisticByUserid();
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->statistic($day_begin_time, $day_end_time, $owner_filter, $order_by, $order_asc, $offset, $limit);
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

    // 获取客户群统计数据 按自然日聚合的方式
    public function getGroupChatStatisticGroupByDay($userid, $day_begin_time, $day_end_time)
    {
        $modelGroupChatStatisticByUserid = new \App\Qyweixin\Models\ExternalContact\GroupChatStatisticByUserid();
        $owner_filter = array();
        $owner_filter['userid_list'] = array($userid);
        $res = $this->getQyWeixinObject()
            ->getExternalContactManager()
            ->getGroupChatManager()
            ->statisticGroupByDay($day_begin_time, $day_end_time, $owner_filter);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //     "errcode": 0,
        //     "errmsg": "ok",
        //     "items": [{
        //             "stat_time": 1600272000,
        //             "data": {
        //                 "new_chat_cnt": 2,
        //                 "chat_total": 2,
        //                 "chat_has_msg": 0,
        //                 "new_member_cnt": 0,
        //                 "member_total": 6,
        //                 "member_has_msg": 0,
        //                 "msg_total": 0,
        //                 "migrate_trainee_chat_cnt": 3
        //             }
        //         },
        //         {
        //             "stat_time": 1600358400,
        //             "data": {
        //                 "new_chat_cnt": 2,
        //                 "chat_total": 2,
        //                 "chat_has_msg": 0,
        //                 "new_member_cnt": 0,
        //                 "member_total": 6,
        //                 "member_has_msg": 0,
        //                 "msg_total": 0,
        //                 "migrate_trainee_chat_cnt": 3
        //             }
        //         }
        //     ]
        // }
        $modelGroupChatStatisticByUserid->syncGroupchatStatisticListGroupByDay($userid, $this->authorizer_appid, $this->provider_appid, $res, time());
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
}
