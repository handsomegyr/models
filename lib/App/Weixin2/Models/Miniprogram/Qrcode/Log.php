<?php

namespace App\Weixin2\Models\Miniprogram\Qrcode;

class Log extends \App\Common\Models\Weixin2\Miniprogram\Qrcode\Log
{
    /**
     * 记录日志
     */
    public function record($authorizer_appid, $component_appid, $FromUserName, $weixin_user_id, $scene, $log_time)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'weixin_user_id' => $weixin_user_id,
            'FromUserName' => $FromUserName,
            'scene' => $scene,
            'log_time' => \App\Common\Utils\Helper::getCurrentTime($log_time)
        );
        return $this->insert($datas);
    }
}
