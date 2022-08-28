<?php

namespace App\Qyweixin\Models\ExternalContact;

class MsgTemplate extends \App\Common\Models\Qyweixin\ExternalContact\MsgTemplate
{
    /**
     * 根据朋友圈id获取信息
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
        /**
         * {
         *     "errcode":0,
         *     "errmsg":"ok",
         *     "next_cursor":"CURSOR",
         *     "group_msg_list":[
         *         {
         *             "msgid":"msgGCAAAXtWyujaWJHDDGi0mAAAA",
         *             "creator":"xxxx",
         *             "create_time":"xxxx",
         *             "create_type":1,
         *             "text": {
         *                 "content":"文本消息内容"
         *             },
         *             "image": {
         *                 "media_id":"WWCISP_XXXXXXX"
         *             },
         *             "link": {
         *                 "title": "消息标题",
         *                 "picurl": "https://example.pic.com/path",
         *                 "desc": "消息描述",
         *                 "url": "https://example.link.com/path"
         *             },
         *             "miniprogram": {
         *                 "title": "消息标题",
         *                 "appid": "wx8bd80126147dfAAA",
         *                 "page": "/path/index.html"
         *             },
         *             "video":{
         *                 "media_id":"WWCISP_XXXXXXX"
         *             }
         *         }
         *     ]
         * }
         */
        if (!empty($res['group_msg_list'])) {
            foreach ($res['group_msg_list'] as $groupmsgInfo) {
                $msgid = $groupmsgInfo['msgid'];
                $create_time = $groupmsgInfo['create_time'];
                $info = $this->getInfoByMsgid($msgid, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['chat_type'] = $chat_type;
                $data['provider_appid'] = $provider_appid;
                $data['creator'] = $groupmsgInfo['creator'];
                $data['create_type'] = $groupmsgInfo['create_type'];
                $data['text_content'] = !isset($groupmsgInfo['text']['content']) ? "" : $groupmsgInfo['text']['content'];
                $data['image_media_id'] = !isset($groupmsgInfo['image']['media_id']) ? "" : $groupmsgInfo['image']['media_id'];
                $data['video_media_id'] = !isset($groupmsgInfo['video']['media_id']) ? "" : $groupmsgInfo['video']['media_id'];
                // $data['video_thumb_media_id'] = !isset($groupmsgInfo['video']['thumb_media_id']) ? "" : $groupmsgInfo['video']['thumb_media_id'];;
                $data['link_title'] = !isset($groupmsgInfo['link']['title']) ? "" : $groupmsgInfo['link']['title'];
                $data['link_picurl'] = !isset($groupmsgInfo['link']['picurl']) ? "" : $groupmsgInfo['link']['picurl'];
                $data['link_desc'] = !isset($groupmsgInfo['link']['desc']) ? "" : $groupmsgInfo['link']['desc'];
                $data['link_url'] = !isset($groupmsgInfo['link']['url']) ? "" : $groupmsgInfo['link']['url'];

                $data['miniprogram_title'] = !isset($groupmsgInfo['miniprogram']['title']) ? "" : $groupmsgInfo['miniprogram']['title'];
                $data['miniprogram_appid'] = !isset($groupmsgInfo['miniprogram']['appid']) ? "" : $groupmsgInfo['miniprogram']['appid'];
                $data['miniprogram_page'] = !isset($groupmsgInfo['miniprogram']['page']) ? "" : $groupmsgInfo['miniprogram']['page'];
                $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($create_time);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['msgid'] = $msgid;
                    $data['name'] = '同步群发消息' . \uniqid();
                    $this->insert($data);
                }
            }
        }
    }

    public function recordMsgId($id, $res, $now)
    {
        $updateData = array();
        $updateData['msgid'] = $res['msgid'];
        $updateData['fail_list'] = \App\Common\Utils\Helper::myJsonEncode($res['fail_list']);
        $updateData['send_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordGroupMsgSendResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['check_status'] = $res['check_status'];
        $updateData['detail_list'] = \App\Common\Utils\Helper::myJsonEncode($res['detail_list']);
        $updateData['get_result_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMediaId($id, $res, $now)
    {
        $updateData = array();
        $updateData['image_media_id'] = $res['media_id'];
        $updateData['image_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMediaId4Miniprogram($id, $res, $now)
    {
        $updateData = array();
        $updateData['miniprogram_pic_media_id'] = $res['media_id'];
        $updateData['miniprogram_pic_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMediaId4Video($id, $res, $now)
    {
        $updateData = array();
        $updateData['video_media_id'] = $res['media_id'];
        $updateData['video_media_created_at'] = \App\Common\Utils\Helper::getCurrentTime($res['created_at']);
        $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
