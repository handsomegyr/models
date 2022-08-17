<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupMsg extends \App\Common\Models\Qyweixin\ExternalContact\GroupMsg
{

    /**
     * 根据消息id获取信息
     *
     * @param string $msgid            
     * @param string $authorizer_appid              
     * @param string $agentid         
     */
    public function getInfoByMsgid($msgid, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['msgid'] = $msgid;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        // $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncGroupmsgList($authorizer_appid, $provider_appid, $agentid, $chat_type, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "next_cursor":"CURSOR",
        //     "group_msg_list":[
        //         {
        //             "msgid":"msgGCAAAXtWyujaWJHDDGi0mAAAA",
        //             "creator":"xxxx",
        //             "create_time":"xxxx",
        //             "create_type":1,
        //             "text": {
        //                 "content":"文本消息内容"
        //             },
        //             "attachments": [
        //                 {
        //                     "msgtype": "image",
        //                     "image": {
        //                         "media_id": "MEDIA_ID",
        //                         "pic_url": "http://p.qpic.cn/pic_wework/3474110808/7a6344sdadfwehe42060/0"
        //                     }
        //                 }, 
        //                 {
        //                     "msgtype": "link",
        //                     "link": {
        //                         "title": "消息标题",
        //                         "picurl": "https://example.pic.com/path",
        //                         "desc": "消息描述",
        //                         "url": "https://example.link.com/path"
        //                     }
        //                 }, 
        //                 {
        //                     "msgtype": "miniprogram",
        //                     "miniprogram": {
        //                         "title": "消息标题",
        //                         "pic_media_id": "MEDIA_ID",
        //                         "appid": "wx8bd80126147dfAAA",
        //                         "page": "/path/index.html"
        //                     }
        //                 },
        //                 {
        //                     "msgtype": "video",
        //                     "video": {
        //                         "media_id": "MEDIA_ID"
        //                     }
        //                 },
        //                 {
        //                     "msgtype": "file",
        //                     "file": {
        //                         "media_id": "MEDIA_ID"
        //                     }
        //                 }
        //             ]
        //         }
        //     ]
        // }
        if (!empty($res['group_msg_list'])) {
            foreach ($res['group_msg_list'] as $groupmsgInfo) {
                $msgid = $groupmsgInfo['msgid'];
                $create_time = $groupmsgInfo['create_time'];
                $info = $this->getInfoByMsgid($msgid, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['creator'] = $groupmsgInfo['creator'];
                $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($create_time);
                $data['create_type'] = $groupmsgInfo['create_type'];
                $data['text_content'] = !isset($groupmsgInfo['text']['content']) ? "" : $groupmsgInfo['text']['content'];
                $data['attachments'] = !isset($groupmsgInfo['attachments']) ? "" : \App\Common\Utils\Helper::myJsonEncode($groupmsgInfo['attachments']);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['msgid'] = $msgid;
                    $this->insert($data);
                }
            }
        }
    }
}
