<?php

namespace App\Qyweixin\Services\Traits;

trait AppchatMsgTrait
{
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
            \App\Common\Utils\Helper::myJsonEncode($appchatmsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $appchatmsg
        );
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
}
