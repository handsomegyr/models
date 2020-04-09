<?php

namespace App\Weixin2\Models\ReplyMsg;

class SendLog extends \App\Common\Models\Weixin2\ReplyMsg\SendLog
{

    /**
     * 记录
     */
    public function record($component_appid, $authorizer_appid, $agentid, $reply_msg_id, $reply_msg_name, $msg_type, $media, $media_id, $thumb_media, $thumb_media_id, $title, $description, $music, $hqmusic, $kf_account, $keyword_id, $keyword, $keyword_reply_msg_type, $ToUserName, $FromUserName, $reply_msg_content, $log_time)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'reply_msg_id' => empty($reply_msg_id) ? 0 : $reply_msg_id,
            'reply_msg_name' => empty($reply_msg_name) ? "" : $reply_msg_name,
            'msg_type' => empty($msg_type) ? "" : $msg_type,
            'media' => empty($media) ? 0 : $media,
            'media_id' => empty($media_id) ? "" : $media_id,
            'thumb_media' => empty($thumb_media) ? 0 : $thumb_media,
            'thumb_media_id' => empty($thumb_media_id) ? "" : $thumb_media_id,
            'title' => empty($title) ? "" : $title,
            'description' => empty($description) ? "" : $description,
            'music' => empty($music) ? "" : $music,
            'hqmusic' => empty($hqmusic) ? "" : $hqmusic,
            'kf_account' => empty($kf_account) ? "" : $kf_account,
            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_reply_msg_type' => empty($keyword_reply_msg_type) ? "" : $keyword_reply_msg_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'reply_msg_content' => empty($reply_msg_content) ? "" : $reply_msg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
