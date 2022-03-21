<?php

namespace App\Qyweixin\Services\Traits;

trait LinkedcorpMsgTrait
{
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
            \App\Common\Utils\Helper::myJsonEncode($linkedcorpmsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $linkedcorpmsg
        );
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
}
