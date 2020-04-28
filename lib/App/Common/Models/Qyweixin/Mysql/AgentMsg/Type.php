<?php

namespace App\Common\Models\Qyweixin\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-应用消息类型
     * This model is mapped to the table iqyweixin_agent_msg_type
     */
    public function getSource()
    {
        return 'iqyweixin_agent_msg_type';
    }
}
