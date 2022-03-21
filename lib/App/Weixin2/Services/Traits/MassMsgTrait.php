<?php

namespace App\Weixin2\Services\Traits;

trait MassMsgTrait
{


    public function sendMassMsg($tag_id, array $toUsers, $massMsgInfo, $sendMethodInfo, $match, $is_send = false)
    {
        $modelMassMsg = new \App\Weixin2\Models\MassMsg\MassMsg();

        $objMassSender = $this->getWeixinObject()
            ->getMsgManager()
            ->getMassSender();

        // 预览用户
        $previewUser = "o2N5jt56GlMdqv46BWxvK0ND-eIw";
        $is_ok = false;
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
                            throw new \Exception($res4UploadVideo['errmsg'] . \App\Common\Utils\Helper::myJsonEncode(array(
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
                    $articles = $modelMassMsgNews->getArticlesByMassMsgId($massMsgInfo['mass_msg_id'], "mpnews");
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
                        throw new \Exception($res4UploadNews['errmsg'] . \App\Common\Utils\Helper::myJsonEncode($articles), $res4UploadNews['errcode']);
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
            $is_ok = true;
            $msg_id = empty($res['msg_id']) ? "" : $res['msg_id'];
            $msg_data_id = empty($res['msg_data_id']) ? "" : $res['msg_data_id'];
            $massmsg = $res;
        } catch (\Exception $e) {
            $massmsg['errorcode'] = $e->getCode();
            $massmsg['errormsg'] = $e->getMessage();
        }

        if (empty($massmsg)) {
            $massmsg = array();
        }

        // 记录日志
        $modelMassMsgSendLog = new \App\Weixin2\Models\MassMsg\SendLog();
        $modelMassMsgSendLog->record($massMsgInfo['component_appid'], $massMsgInfo['authorizer_appid'], $massMsgInfo['_id'], $massMsgInfo['name'], $massMsgInfo['msg_type'], $massMsgInfo['media'], $massMsgInfo['media_id'], $massMsgInfo['thumb_media'], $massMsgInfo['thumb_media_id'], $massMsgInfo['title'], $massMsgInfo['description'], $massMsgInfo['card_id'], $massMsgInfo['card_ext'], $massMsgInfo['upload_media_id'], $massMsgInfo['upload_media_created_at'], $massMsgInfo['upload_media_type'], $is_to_all, $tag_id, \App\Common\Utils\Helper::myJsonEncode($toUsers), $send_ignore_reprint, $clientmsgid, $match['_id'], $match['keyword'], $match['mass_msg_type'], "", "", \App\Common\Utils\Helper::myJsonEncode($massmsg), $msg_id, $msg_data_id, time());

        return array(
            'is_ok' => $is_ok,
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
}
