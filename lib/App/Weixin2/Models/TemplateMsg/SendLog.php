<?php

namespace App\Weixin2\Models\TemplateMsg;

class SendLog extends \App\Common\Models\Weixin2\TemplateMsg\SendLog
{

    /**
     * 记录
     */
    public function record($component_appid, $authorizer_appid, $template_msg_id, $template_msg_name, $template_id, $url, $data, $color, $appid, $pagepath, $keyword_id, $keyword, $ToUserName, $FromUserName, $template_msg_content, $log_time, $send_method)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'template_msg_id' => empty($template_msg_id) ? 0 : $template_msg_id,
            'template_msg_name' => empty($template_msg_name) ? "" : $template_msg_name,
            'template_id' => empty($template_id) ? "" : $template_id,
            'url' => empty($url) ? "" : $url,
            'data' => empty($data) ? "" : $data,
            'color' => empty($color) ? "" : $color,
            'appid' => empty($appid) ? "" : $appid,
            'pagepath' => empty($pagepath) ? "" : $pagepath,
            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'template_msg_content' => empty($template_msg_content) ? "" : $template_msg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time),
            'send_method' => intval($send_method)
        );
        return $this->insert($datas);
    }
}
