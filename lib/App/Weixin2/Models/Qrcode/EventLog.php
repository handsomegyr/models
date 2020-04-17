<?php

namespace App\Weixin2\Models\Qrcode;

class EventLog extends \App\Common\Models\Weixin2\Qrcode\EventLog
{

    /**
     * 记录日志
     */
    public function record($authorizer_appid, $component_appid, $agentid, $scene, $FromUserName, $ToUserName, $CreateTime, $MsgType, $Event, $EventKey, $Ticket)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'scene' => $scene,
            'ToUserName' => $ToUserName,
            'FromUserName' => $FromUserName,
            'CreateTime' => $CreateTime,
            'MsgType' => $MsgType,
            'Event' => $Event,
            'EventKey' => $EventKey,
            'Ticket' => $Ticket
        );
        return $this->insert($datas);
    }
}
