<?php

namespace App\Weixin2\Services\Traits;

trait ReplyMsgsTrait
{
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

        // 交换一下
        $tmp1 = $ToUserName;
        $ToUserName = $FromUserName;
        $FromUserName = $tmp1;

        switch ($match['reply_msg_type']) {
            case 'news':
                $articles = array();
                // 获取图文列表
                $isFirst = empty($articles) ? true : false;
                $modelReplyMsgNews = new \App\Weixin2\Models\ReplyMsg\News();
                $articles1 = $modelReplyMsgNews->getArticlesByReplyMsgId($replyMsgs[0]['_id'], 'news', $isFirst);
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
        $modelReplyMsgSendLog->record($replyMsgs[0]['component_appid'], $replyMsgs[0]['authorizer_appid'], $replyMsgs[0]['_id'], $replyMsgs[0]['name'], $replyMsgs[0]['msg_type'], $replyMsgs[0]['media'], $replyMsgs[0]['media_id'], $replyMsgs[0]['thumb_media'], $replyMsgs[0]['thumb_media_id'], $replyMsgs[0]['title'], $replyMsgs[0]['description'], $replyMsgs[0]['music'], $replyMsgs[0]['hqmusic'], $replyMsgs[0]['kf_account'], $match['_id'], $match['keyword'], $match['reply_msg_type'], $ToUserName, $FromUserName, $replymsg, time());

        return $replymsg;
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
}
