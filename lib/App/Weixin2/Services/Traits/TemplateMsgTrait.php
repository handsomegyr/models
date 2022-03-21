<?php

namespace App\Weixin2\Services\Traits;

trait TemplateMsgTrait
{

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

        $modelTemplate->removeCreatedStatus($template_rec_id, time());

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
        $sendRet = $this->sendTemplateMsg($ToUserName, $FromUserName,  $templates[0], $match);
        return $sendRet['is_ok'];
    }

    public function sendTemplateMsg($FromUserName, $ToUserName, $templateMsgInfo, $match)
    {
        $objWeixin = $this->getWeixinObject();
        $templatemsg = array();
        $is_ok = false;
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
                ->send($ToUserName, $templateMsgInfo['template_id'], $templateMsgInfo['url'], $templateMsgInfo['color'], $data, $miniprogram);

            if (!empty($templatemsg['errcode'])) {
                throw new \Exception($templatemsg['errmsg'], $templatemsg['errcode']);
            }
            $is_ok = true;
        } catch (\Exception $e) {
            $templatemsg['errorcode'] = $e->getCode();
            $templatemsg['errormsg'] = $e->getMessage();
        }

        if (empty($templatemsg)) {
            $templatemsg = array();
        }

        // 记录日志
        $modelTemplateMsgSendLog = new \App\Weixin2\Models\TemplateMsg\SendLog();
        $modelTemplateMsgSendLog->record($templateMsgInfo['component_appid'], $templateMsgInfo['authorizer_appid'], $templateMsgInfo['_id'], $templateMsgInfo['name'], $templateMsgInfo['template_id'], $templateMsgInfo['url'], $templateMsgInfo['data'], $templateMsgInfo['color'], $templateMsgInfo['appid'], $templateMsgInfo['pagepath'], $match['_id'], $match['keyword'], $ToUserName, $FromUserName, \App\Common\Utils\Helper::myJsonEncode($templatemsg), time(), 0);

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $templatemsg
        );
    }
}
