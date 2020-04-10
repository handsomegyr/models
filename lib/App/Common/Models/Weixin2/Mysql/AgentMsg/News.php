<?php

namespace App\Common\Models\Weixin2\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 微信-应用消息图文
     * This model is mapped to the table iweixin2_agent_msg_news
     */
    public function getSource()
    {
        return 'iweixin2_agent_msg_news';
    }
}
