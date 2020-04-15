<?php

namespace App\Common\Models\Weixin2\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 微信-互联企业消息图文
     * This model is mapped to the table iweixin2_linkedcorp_msg_news
     */
    public function getSource()
    {
        return 'iweixin2_linkedcorp_msg_news';
    }
}
