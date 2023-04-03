<?php

namespace App\Weixin2\Services\Traits;

trait CustomMsgsTrait
{

    public function answerCustomMsgs($FromUserName, $ToUserName, $match)
    {
        $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
        $customMsgs = $modelCustomMsg->getCustomMsgsByKeyword($match);
        if (empty($customMsgs)) {
            return false;
        }

        // 最多发3条
        foreach ($customMsgs as $idx => $customMsgInfo) {
            if ($idx < 3) {
                $sendRet = $this->sendCustomMsg($ToUserName, $FromUserName, $customMsgInfo, $match);
            }
        }
        return true;
    }

    public function sendCustomMsg($FromUserName, $ToUserName, $customMsgInfo, $match)
    {
        $objWeixin = $this->getWeixinObject();
        $custommsg = array();
        $is_ok = false;
        try {
            switch ($customMsgInfo['msg_type']) {
                case 'news':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $articles = array();
                    // 获取图文列表
                    $isFirst = empty($articles) ? true : false;
                    $modelCustomMsgNews = new \App\Weixin2\Models\CustomMsg\News();
                    $articles1 = $modelCustomMsgNews->getArticlesByCustomMsgId($customMsgInfo['_id'], 'news', $isFirst);
                    $articles = array_merge($articles, $articles1);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendGraphText($ToUserName, $articles);
                    break;
                case 'music':
                    $modelCustomMsg = new \App\Weixin2\Models\CustomMsg\CustomMsg();
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $thumb_media_id = $this->getMediaId4CustomMsg('thumb', $customMsgInfo);
                    $hqmusic = empty($customMsgInfo['hqmusic']) ? "" : $modelCustomMsg->getPhysicalFilePath($customMsgInfo['hqmusic']);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMusic($ToUserName, $customMsgInfo['title'], $customMsgInfo['description'], $modelCustomMsg->getPhysicalFilePath($customMsgInfo['music']), $hqmusic, $thumb_media_id);
                    break;
                case 'text':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendText($ToUserName, $customMsgInfo['description']);
                    break;
                case 'voice':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('voice', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendVoice($ToUserName, $media_id);
                    break;
                case 'video':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('video', $customMsgInfo);
                    $thumb_media_id = $this->getMediaId4CustomMsg('thumb', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendVideo($ToUserName, $media_id, $thumb_media_id, $customMsgInfo['title'], $customMsgInfo['description']);
                    break;
                case 'image':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('image', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendImage($ToUserName, $media_id);
                    break;
                case 'mpnews':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $media_id = $this->getMediaId4CustomMsg('mpnews', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMpNews($ToUserName, $media_id);
                    break;
                case 'mpnewsarticle':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $article_id = $customMsgInfo['article_id'];
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMpNewsArticle($ToUserName, $article_id);
                    break;
                case 'msgmenu':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $msgmenu = empty($customMsgInfo['description']) ? array() : \json_decode($customMsgInfo['description'], true);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMsgMenu($ToUserName, $msgmenu);
                    break;
                case 'miniprogrampage':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $thumb_media_id = $this->getMediaId4CustomMsg('thumb', $customMsgInfo);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendMiniProgramPage($ToUserName, $customMsgInfo['title'], $customMsgInfo['appid'], $customMsgInfo['pagepath'], $thumb_media_id);
                    break;
                case 'wxcard':
                    $kf_account = empty($customMsgInfo['kf_account']) ? "" : $customMsgInfo['kf_account'];
                    $card_ext = empty($customMsgInfo['card_ext']) ? array() : \json_decode($customMsgInfo['card_ext'], true);
                    $custommsg = $objWeixin->getMsgManager()
                        ->getCustomSender()
                        ->setKfAccount($kf_account)
                        ->sendWxcard($ToUserName, $customMsgInfo['card_id'], $card_ext);
                    break;
            }

            if (!empty($custommsg['errcode'])) {
                throw new \Exception($custommsg['errmsg'], $custommsg['errcode']);
            }
            $is_ok = true;
        } catch (\Exception $e) {
            $custommsg['errorcode'] = $e->getCode();
            $custommsg['errormsg'] = $e->getMessage();
        }

        if (empty($custommsg)) {
            $custommsg = array();
        }
        // 记录日志
        $modelCustomMsgSendLog = new \App\Weixin2\Models\CustomMsg\SendLog();
        $modelCustomMsgSendLog->record(
            $customMsgInfo['component_appid'],
            $customMsgInfo['authorizer_appid'],
            $customMsgInfo['_id'],
            $customMsgInfo['name'],
            $customMsgInfo['msg_type'],
            $customMsgInfo['media'],
            $customMsgInfo['media_id'],
            $customMsgInfo['thumb_media'],
            $customMsgInfo['thumb_media_id'],
            $customMsgInfo['title'],
            $customMsgInfo['description'],
            $customMsgInfo['music'],
            $customMsgInfo['hqmusic'],
            $customMsgInfo['appid'],
            $customMsgInfo['pagepath'],
            $customMsgInfo['card_id'],
            $customMsgInfo['card_ext'],
            $customMsgInfo['article_id'],
            $customMsgInfo['kf_account'],
            $match['_id'],
            $match['keyword'],
            $match['custom_msg_type'],
            $ToUserName,
            $FromUserName,
            \App\Common\Utils\Helper::myJsonEncode($custommsg),
            time()
        );

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $custommsg
        );
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
