<?php

namespace App\Common\Models\Qyweixin\Mysql\Keyword;

use App\Common\Models\Base\Mysql\Base;

class KeywordToAgentMsg extends Base
{

    /**
     * 企业微信-关键词和应用消息对应
     * This model is mapped to the table iqyweixin_keyword_to_agentmsg
     */
    public function getSource()
    {
        return 'iqyweixin_keyword_to_agentmsg';
    }
}
