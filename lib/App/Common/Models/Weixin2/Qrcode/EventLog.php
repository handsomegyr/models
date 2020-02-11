<?php

namespace App\Common\Models\Weixin2\Qrcode;

use App\Common\Models\Base\Base;

class EventLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Qrcode\EventLog());
    }

    public function record($authorizer_appid, $component_appid, $FromUserName, $ToUserName, $CreateTime, $MsgType, $Event, $EventKey, $Ticket)
    {
        $scene = $EventKey;

        if ($Event === 'subscribe') {
            $scene = str_ireplace('qrscene_', '', $EventKey);
        }
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
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
