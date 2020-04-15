<?php

namespace App\Weixin2\Models\LinkedcorpMsg;

class SendMethod extends \App\Common\Models\Weixin2\LinkedcorpMsg\SendMethod
{
    //互联企业消息发送方式 0:发送给应用可见范围内的所有人 1:按成员ID列表发送 2:按部门ID列表发送 3:按标签ID列表发送    
    const SEND_ALL = 0;

    const SEND_BY_USER = 1;

    const SEND_BY_PARTY = 2;

    const SEND_BY_TAG = 3;
}
