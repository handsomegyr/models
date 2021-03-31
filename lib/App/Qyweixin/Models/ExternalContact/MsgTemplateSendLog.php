<?php

namespace App\Qyweixin\Models\ExternalContact;

class MsgTemplateSendLog extends \App\Common\Models\Qyweixin\ExternalContact\MsgTemplateSendLog
{
    /**
     * 记录
     */
    public function record(
        $provider_appid,
        $authorizer_appid,
        $agentid,
        $msg_template_id,
        $msg_template_name,
        $chat_type,
        $external_userid,
        $sender,
        $text_content,
        $image_media,
        $image_media_id,
        $image_pic_url,
        $image_media_created_at,
        $link_title,
        $link_picurl,
        $link_desc,
        $link_url,
        $miniprogram_title,
        $miniprogram_pic_media,
        $miniprogram_pic_media_id,
        $miniprogram_pic_media_created_at,
        $miniprogram_appid,
        $miniprogram_page,
        $keyword_id,
        $keyword,
        $keyword_msg_template_chat_type,
        $ToUserName,
        $FromUserName,
        $msg_template_content,
        $log_time
    ) {
        $datas = array(
            'provider_appid' => $provider_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'msg_template_id' => empty($msg_template_id) ? "" : $msg_template_id,
            'msg_template_name' => empty($msg_template_name) ? "" : $msg_template_name,
            'chat_type' => empty($chat_type) ? "" : $chat_type,
            'external_userid' => empty($external_userid) ? "" : \json_encode($external_userid),
            'sender' => empty($sender) ? "" : $sender,

            'text_content' => empty($text_content) ? "" : $text_content,

            'image_media' => empty($image_media) ? "" : $image_media,
            'image_media_id' => empty($url) ? '' : $image_media_id,
            'image_pic_url' => empty($image_pic_url) ? "" : $image_pic_url,
            'image_media_created_at' => empty($image_media_created_at) ? \App\Common\Utils\Helper::getCurrentTime(strtotime("0001-01-01 00:00:00")) : $image_media_created_at,

            'link_title' => empty($link_title) ? "" : $link_title,
            'link_picurl' => empty($link_picurl) ? "" : $link_picurl,
            'link_desc' => empty($link_desc) ? "" : $link_desc,
            'link_url' => empty($link_url) ? "" : $link_url,

            'miniprogram_title' => empty($miniprogram_title) ? "" : $miniprogram_title,
            'miniprogram_pic_media' => empty($miniprogram_pic_media) ? "" : $miniprogram_pic_media,
            'miniprogram_pic_media_id' => empty($miniprogram_pic_media_id) ? "" : $miniprogram_pic_media_id,
            'miniprogram_pic_media_created_at' => empty($miniprogram_pic_media_created_at) ? \App\Common\Utils\Helper::getCurrentTime(strtotime("0001-01-01 00:00:00")) : $miniprogram_pic_media_created_at,
            'miniprogram_appid' => empty($miniprogram_appid) ? "" : $miniprogram_appid,
            'miniprogram_page' => empty($miniprogram_page) ? "" : $miniprogram_page,

            'keyword_id' => empty($keyword_id) ? "" : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_msg_template_chat_type' => empty($keyword_msg_template_chat_type) ? "" : $keyword_msg_template_chat_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'msg_template_content' => empty($msg_template_content) ? "" : $msg_template_content,
            'send_time' => \App\Common\Utils\Helper::getCurrentTime($log_time),
            'msgid' =>  "",
            'fail_list' =>  "[]",
            'check_status' =>  0,
            'detail_list' =>  "[]",
            'get_result_time' =>  \App\Common\Utils\Helper::getCurrentTime(strtotime("0001-01-01 00:00:00")),
        );

        if (!empty($msg_template_content)) {
            $res = \json_decode($msg_template_content, true);
            if (isset($res['msgid'])) {
                $datas['msgid'] = $res['msgid'];
                $datas['fail_list'] = \json_encode($res['fail_list']);
            }
        }

        return $this->insert($datas);
    }

    public function recordMsgId($id, $res, $now)
    {
        $updateData = array();
        $updateData['msgid'] = $res['msgid'];
        $updateData['fail_list'] = \json_encode($res['fail_list']);
        $updateData['send_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordGroupMsgResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['check_status'] = $res['check_status'];
        $updateData['detail_list'] = \json_encode($res['detail_list']);
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
}
