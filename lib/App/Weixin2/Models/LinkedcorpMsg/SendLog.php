<?php

namespace App\Weixin2\Models\LinkedcorpMsg;

class SendLog extends \App\Common\Models\Weixin2\LinkedcorpMsg\SendLog
{

    /**
     * 记录
     */
    public function record(
        $component_appid,
        $authorizer_appid,
        $agentid,
        $linkedcorp_msg_id,
        $linkedcorp_msg_name,
        $msg_type,
        $media,
        $media_id,
        $title,
        $description,
        $url,
        $btntxt,
        $appid,
        $pagepath,
        $emphasis_first_item,
        $content_item,
        $toall,
        $safe,
        $keyword_id,
        $keyword,
        $keyword_linkedcorp_msg_type,
        $ToUserName,
        $FromUserName,
        $linkedcorp_msg_content,
        $log_time
    ) {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'linkedcorp_msg_id' => empty($linkedcorp_msg_id) ? 0 : $linkedcorp_msg_id,
            'linkedcorp_msg_name' => empty($linkedcorp_msg_name) ? "" : $linkedcorp_msg_name,
            'msg_type' => empty($msg_type) ? "" : $msg_type,
            'media' => empty($media) ? 0 : $media,
            'media_id' => empty($media_id) ? "" : $media_id,
            'title' => empty($title) ? "" : $title,
            'description' => empty($description) ? "" : $description,
            'url' => empty($url) ? '' : $url,
            'btntxt' => empty($btntxt) ? "" : $btntxt,
            'appid' => empty($appid) ? "" : $appid,
            'pagepath' => empty($pagepath) ? "" : $pagepath,
            'emphasis_first_item' => empty($emphasis_first_item) ? "0" : $emphasis_first_item,
            'content_item' => empty($content_item) ? "" : $content_item,
            'enable_id_trans' => empty($toall) ? "0" : $toall,
            'safe' => empty($safe) ? "0" : $safe,
            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_linkedcorp_msg_type' => empty($keyword_linkedcorp_msg_type) ? "" : $keyword_linkedcorp_msg_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'linkedcorp_msg_content' => empty($linkedcorp_msg_content) ? "" : $linkedcorp_msg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
