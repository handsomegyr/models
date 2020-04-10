<?php

namespace App\Common\Models\Weixin2\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class SendMethod extends Base
{
    /**
     * 微信-应用消息发送方式
     * This model is mapped to the table iweixin2_agent_msg_send_method
     */
    public function getSource()
    {
        return 'iweixin2_agent_msg_send_method';
    }
}
