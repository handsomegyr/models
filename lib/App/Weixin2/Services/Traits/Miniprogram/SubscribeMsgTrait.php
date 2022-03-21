<?php

namespace App\Weixin2\Services\Traits\Miniprogram;

trait SubscribeMsgTrait
{
    //通过获取模板配置后发送小程序订阅消息
    public function doSendMicroappSubscribeMsg($openid, $callback4GetTemplateMsgParams, $templateMsgInfo, $now)
    {
        // 调用小程序订阅消息推送
        // 设置订阅消息参数
        $templateMsgParams = call_user_func_array($callback4GetTemplateMsgParams, array(
            $templateMsgInfo
        ));

        // 发送小程序发送订阅消息     
        $res = $this->subscribeMsgSend($openid, $templateMsgParams['template_id'], $templateMsgParams['data'], $templateMsgParams['page']);
        if (!empty($res['errcode'])) {
            $templateMsgParams2 = \App\Common\Utils\Helper::myJsonEncode($templateMsgParams);
            throw new \Exception("openid为{$openid}的订阅消息发送失败，错误：{$res['errmsg']}，参数：{$templateMsgParams2}", $res['errcode']);
        }
        return $res;
    }

    //发送小程序订阅消息
    public function subscribeMsgSend($touser, $template_id, array $data, $page = '', $miniprogram_state = '', $lang = "")
    {
        $weixin = $this->getWeixinObject();
        $res = $weixin->getWxClient()
            ->getMsgManager()
            ->getSubscribeMessageSender()
            ->send($touser, $template_id, $data, $page, $miniprogram_state, $lang);
        return $res;
    }

    //发送小程序统一消息
    public function uniformSend($touser, array $mp_template_msg, array $weapp_template_msg)
    {
        $weixin = $this->getWeixinObject();
        $res = $weixin->getWxClient()
            ->getMsgManager()
            ->getTemplateSender()
            ->uniformSend($touser, $mp_template_msg, $weapp_template_msg);
        return $res;
    }

    //发送小程序订阅消息(推送用)
    public function sendMicroappSubscribeMsg($FromUserName, $ToUserName, $subscribeMsgInfo, $match)
    {
        $templatemsg = array();
        $is_ok = false;
        try {
            $data = empty($subscribeMsgInfo['data']) ? array() : (!is_array($subscribeMsgInfo['data']) ? \json_decode($subscribeMsgInfo['data'], true) : $subscribeMsgInfo['data']);
            $templatemsg = $this->subscribeMsgSend($ToUserName, $subscribeMsgInfo['template_id'], $data, $subscribeMsgInfo['pageurl'], $subscribeMsgInfo['miniprogram_state'], $subscribeMsgInfo['lang']);

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
        $modelSubscribeMsgSendLog = new \App\Weixin2\Models\Miniprogram\SubscribeMsg\SendLog();
        $modelSubscribeMsgSendLog->record($subscribeMsgInfo['component_appid'], $subscribeMsgInfo['authorizer_appid'], $subscribeMsgInfo['id'], $subscribeMsgInfo['name'], $subscribeMsgInfo['template_id'], $subscribeMsgInfo['data'], $subscribeMsgInfo['pageurl'], $subscribeMsgInfo['miniprogram_state'], $subscribeMsgInfo['lang'], $match['id'], $match['keyword'], $ToUserName, $FromUserName, \App\Common\Utils\Helper::myJsonEncode($templatemsg), time());

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $templatemsg
        );
    }

    //发送小程序统一消息(推送用)
    public function sendMicroappUniformMsg($FromUserName, $ToUserName, $templateMsgInfo, $match)
    {
        $templatemsg = array();
        $is_ok = false;

        try {
            $appid = empty($templateMsgInfo['authorizer_appid']) ? "" : $templateMsgInfo['authorizer_appid'];
            $template_id = empty($templateMsgInfo['template_id']) ? "" : $templateMsgInfo['template_id'];
            $data = empty($templateMsgInfo['data']) ? array() : \json_decode($templateMsgInfo['data'], true);
            $url = empty($templateMsgInfo['url']) ? "" : $templateMsgInfo['url'];
            $appid4miniapp = empty($templateMsgInfo['appid']) ? "" : $templateMsgInfo['appid'];
            $pagepath4miniapp = empty($templateMsgInfo['pagepath']) ? "" : $templateMsgInfo['pagepath'];
            $miniprogram = NULL;
            if (!empty($appid4miniapp)) {
                $miniprogram['appid'] = $appid4miniapp;
            }
            if (!empty($pagepath4miniapp)) {
                $miniprogram['pagepath'] = $pagepath4miniapp;
            }

            /**
             * "mp_template_msg":{
             *      "appid":"APPID ",
             *      "template_id":"TEMPLATE_ID",
             *      "url":"http://weixin.qq.com/download",
             *      "miniprogram":{
             *          "appid":"xiaochengxuappid12345",
             *          "pagepath":"index?foo=bar"
             *      },
             *      "data":{
             *          "first":{
             *             "value":"恭喜你购买成功！",
             *             "color":"#173177"
             *          },
             *          "keyword1":{
             *             "value":"巧克力",
             *             "color":"#173177"
             *          },
             *          "keyword2":{
             *             "value":"39.8元",
             *             "color":"#173177"
             *          },
             *          "keyword3":{
             *             "value":"2014年9月22日",
             *             "color":"#173177"
             *          },
             *          "remark":{
             *             "value":"欢迎再次购买！",
             *             "color":"#173177"
             *          }
             *      }
             *  }
             */
            $mp_template_msg = array();
            $mp_template_msg['appid'] = $appid;
            $mp_template_msg['template_id'] = $template_id;
            $mp_template_msg['url'] = $url;
            $mp_template_msg['miniprogram'] = $miniprogram;
            $mp_template_msg['data'] = $data;

            $templatemsg = $this->uniformSend($ToUserName, $mp_template_msg, array());
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
        $modelTemplateMsgSendLog->record($templateMsgInfo['component_appid'], $templateMsgInfo['authorizer_appid'], $templateMsgInfo['id'], $templateMsgInfo['name'], $templateMsgInfo['template_id'], $templateMsgInfo['url'], $templateMsgInfo['data'], $templateMsgInfo['color'], $templateMsgInfo['appid'], $templateMsgInfo['pagepath'], $match['id'], $match['keyword'], $ToUserName, $FromUserName, \App\Common\Utils\Helper::myJsonEncode($templatemsg), time(), 1);

        return array(
            'is_ok' => $is_ok,
            'api_ret' => $templatemsg
        );
    }

    // 同步小程序订阅模板列表
    public function syncMicroappSubscribeMsgTemplateList()
    {
        $modelTemplate = new \App\Weixin2\Models\Miniprogram\SubscribeMsg\Template\Template();
        $weixin = $this->getWeixinObject();
        $res = $weixin->getWxClient()
            ->getMsgManager()
            ->getSubscribeMessageSender()
            ->getTemplateList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         *   "errcode": 0,
         *   "errmsg": "ok",
         *   "data": [
         *       {
         *           "priTmplId": "9Aw5ZV1j9xdWTFEkqCpZ7mIBbSC34khK55OtzUPl0rU",
         *           "title": "报名结果通知",
         *           "content": "会议时间:{{date2.DATA}}\n会议地点:{{thing1.DATA}}\n",
         *           "example": "会议时间:2016年8月8日\n会议地点:TIT会议室\n",
         *           "type": 2
         *       }
         *   ]
         *   }
         */
        $modelTemplate->syncTemplateList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }
}
