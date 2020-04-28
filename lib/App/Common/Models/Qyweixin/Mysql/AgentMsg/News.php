<?php

namespace App\Common\Models\Qyweixin\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 企业微信-应用消息图文
     * This model is mapped to the table iqyweixin_agent_msg_news
     */
    public function getSource()
    {
        return 'iqyweixin_agent_msg_news';
    }
}
