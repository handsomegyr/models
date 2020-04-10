<?php

namespace App\Common\Models\Weixin2\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-应用消息类型
     * This model is mapped to the table iweixin2_agent_msg_type
     */
    public function getSource()
    {
        return 'iweixin2_agent_msg_type';
    }
}
