<?php

namespace App\Common\Models\Qyweixin\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class SendMethod extends Base
{
    /**
     * 企业微信-应用消息发送方式
     * This model is mapped to the table iqyweixin_agent_msg_send_method
     */
    public function getSource()
    {
        return 'iqyweixin_agent_msg_send_method';
    }
}
