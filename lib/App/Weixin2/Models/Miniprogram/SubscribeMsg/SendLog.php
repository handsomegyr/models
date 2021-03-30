<?php

namespace App\Weixin2\Models\Miniprogram\SubscribeMsg;

class SendLog extends \App\Common\Models\Weixin2\Miniprogram\SubscribeMsg\SendLog
{
    /**
     * 记录
     */
    public function record($component_appid, $authorizer_appid, $subscribemsg_id, $subscribemsg_name, $template_id, $data, $pageurl, $miniprogram_state, $lang, $keyword_id, $keyword, $ToUserName, $FromUserName, $subscribemsg_content, $log_time)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'subscribemsg_id' => empty($subscribemsg_id) ? "" : $subscribemsg_id,
            'subscribemsg_name' => empty($subscribemsg_name) ? "" : $subscribemsg_name,
            'template_id' => empty($template_id) ? "" : $template_id,
            'data' => empty($data) ? "" : $data,
            'pageurl' => empty($pageurl) ? "" : $pageurl,
            'miniprogram_state' => empty($miniprogram_state) ? "" : $miniprogram_state,
            'lang' => empty($lang) ? "" : $lang,
            'keyword_id' => empty($keyword_id) ? "" : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'subscribemsg_content' => empty($subscribemsg_content) ? "" : $subscribemsg_content,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
