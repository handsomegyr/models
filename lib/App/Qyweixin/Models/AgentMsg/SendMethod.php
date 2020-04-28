<?php

namespace App\Qyweixin\Models\AgentMsg;

class SendMethod extends \App\Common\Models\Qyweixin\AgentMsg\SendMethod
{
    //应用消息发送方式 0:向关注该企业应用的全部成员发送 1:按成员ID列表发送 2:按部门ID列表发送 3:按标签ID列表发送    
    const SEND_ALL = 0;

    const SEND_BY_USER = 1;

    const SEND_BY_PARTY = 2;

    const SEND_BY_TAG = 3;
}
