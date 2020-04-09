<?php

namespace App\Weixin2\Models\CustomMsg;

class SendLog extends \App\Common\Models\Weixin2\CustomMsg\SendLog
{

    /**
     * 记录
     */
    public function record($component_appid, $authorizer_appid, $agentid, $custom_msg_id, $custom_msg_name, $msg_type, $media, $media_id, $thumb_media, $thumb_media_id, $title, $description, $music, $hqmusic, $appid, $pagepath, $card_id, $card_ext, $kf_account, $keyword_id, $keyword, $keyword_custom_msg_type, $ToUserName, $FromUserName, $custom_msg_content, $log_time)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'custom_msg_id' => empty($custom_msg_id) ? 0 : $custom_msg_id,
            'custom_msg_name' => empty($custom_msg_name) ? "" : $custom_msg_name,
            'msg_type' => empty($msg_type) ? "" : $msg_type,
            'media' => empty($media) ? 0 : $media,
            'media_id' => empty($media_id) ? "" : $media_id,
            'thumb_media' => empty($thumb_media) ? 0 : $thumb_media,
            'thumb_media_id' => empty($thumb_media_id) ? "" : $thumb_media_id,
            'title' => empty($title) ? "" : $title,
            'description' => empty($description) ? "" : $description,
            'music' => empty($music) ? "" : $music,
            'hqmusic' => empty($hqmusic) ? "" : $hqmusic,
            'appid' => empty($appid) ? "" : $appid,
            'pagepath' => empty($pagepath) ? "" : $pagepath,
            'card_id' => empty($card_id) ? "" : $card_id,
            'card_ext' => empty($card_ext) ? "" : $card_ext,
            'kf_account' => empty($kf_account) ? "" : $kf_account,
            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_custom_msg_type' => empty($keyword_custom_msg_type) ? "" : $keyword_custom_msg_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'custom_msg_content' => empty($custom_msg_content) ? "" : $custom_msg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
