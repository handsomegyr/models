<?php

namespace App\Qyweixin\Services\Traits;

trait AgentMsgsTrait
{
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
            \App\Common\Utils\Helper::myJsonEncode($agentmsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $agentmsg
        );
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
}
