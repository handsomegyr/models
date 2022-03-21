<?php

namespace App\Qyweixin\Services\Traits;

trait ReplyMsgTrait
{
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

    private function getMediaId4ReplyMsg($type, $reply)
    {
        if (!empty($reply['media'])) {
            $media_result = $this->uploadMedia($reply['media']);
            return $media_result['media_id'];
        } else {
            throw new \Exception("未指定临时素材", 99999);
        }
    }
}
