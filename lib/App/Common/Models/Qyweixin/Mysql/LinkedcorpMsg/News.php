<?php

namespace App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 企业微信-互联企业消息图文
     * This model is mapped to the table iqyweixin_linkedcorp_msg_news
     */
    public function getSource()
    {
        return 'iqyweixin_linkedcorp_msg_news';
    }
}
