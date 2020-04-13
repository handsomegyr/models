<?php

namespace App\Weixin2\Models\AgentMsg;

class SendLog extends \App\Common\Models\Weixin2\AgentMsg\SendLog
{

    /**
     * 记录
     */
    public function record(
        $component_appid,
        $authorizer_appid,
        $agentid,
        $agent_msg_id,
        $agent_msg_name,
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
        $task_id,
        $btn,
        $safe,
        $enable_id_trans,
        $enable_duplicate_check,
        $duplicate_check_interval,
        $keyword_id,
        $keyword,
        $keyword_agent_msg_type,
        $ToUserName,
        $FromUserName,
        $agent_msg_content,
        $log_time
    ) {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'agent_msg_id' => empty($agent_msg_id) ? 0 : $agent_msg_id,
            'agent_msg_name' => empty($agent_msg_name) ? "" : $agent_msg_name,
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
            'task_id' => empty($task_id) ? "" : $task_id,
            'btn' => empty($btn) ? "" : $btn,
            'safe' => empty($safe) ? "0" : $safe,
            'enable_id_trans' => empty($enable_id_trans) ? "0" : $enable_id_trans,
            'enable_duplicate_check' => empty($enable_duplicate_check) ? "0" : $enable_duplicate_check,
            'duplicate_check_interval' => empty($duplicate_check_interval) ? "0" : $duplicate_check_interval,

            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_agent_msg_type' => empty($keyword_agent_msg_type) ? "" : $keyword_agent_msg_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'agent_msg_content' => empty($agent_msg_content) ? "" : $agent_msg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
