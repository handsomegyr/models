<?php

namespace App\Common\Models\Weixin2\Mysql\Qrcode;

use App\Common\Models\Base\Mysql\Base;

class EventLog extends Base
{
    /**
     * 微信-二维码事件推送日志
     * This model is mapped to the table iweixin2_qrcode_event_log
     */
    public function getSource()
    {
        return 'iweixin2_qrcode_event_log';
    }
}
