<?php

namespace App\Qyweixin\Models\AppchatMsg;

class SendLog extends \App\Common\Models\Qyweixin\AppchatMsg\SendLog
{

    /**
     * 记录
     */
    public function record(
        $provider_appid,
        $authorizer_appid,
        $agentid,
        $chatid,
        $appchat_msg_id,
        $appchat_msg_name,
        $msg_type,
        $media,
        $media_id,
        $title,
        $description,
        $url,
        $btntxt,
        $safe,
        $keyword_id,
        $keyword,
        $keyword_appchat_msg_type,
        $ToUserName,
        $FromUserName,
        $appchat_msg_content,
        $log_time
    ) {
        $datas = array(
            'provider_appid' => $provider_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'chatid' => $chatid,
            'appchat_msg_id' => empty($appchat_msg_id) ? 0 : $appchat_msg_id,
            'appchat_msg_name' => empty($appchat_msg_name) ? "" : $appchat_msg_name,
            'msg_type' => empty($msg_type) ? "" : $msg_type,
            'media' => empty($media) ? 0 : $media,
            'media_id' => empty($media_id) ? "" : $media_id,
            'title' => empty($title) ? "" : $title,
            'description' => empty($description) ? "" : $description,
            'url' => empty($url) ? '' : $url,
            'btntxt' => empty($btntxt) ? "" : $btntxt,
            'safe' => empty($safe) ? "0" : $safe,
            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_appchat_msg_type' => empty($keyword_appchat_msg_type) ? "" : $keyword_appchat_msg_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'appchat_msg_content' => empty($appchat_msg_content) ? "" : $appchat_msg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
