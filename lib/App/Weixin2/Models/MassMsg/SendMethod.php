<?php

namespace App\Weixin2\Models\MassMsg;

class SendMethod extends \App\Common\Models\Weixin2\MassMsg\SendMethod
{
    // 群发消息发送方式 0:全部发送 1:按tag_id发送 2:按openid列表
    const SEND_ALL = 0;

    const SEND_BY_TAGID = 1;

    const SEND_BY_OPENIDS = 2;
}
