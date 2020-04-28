<?php

namespace App\Common\Models\Qyweixin\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 企业微信-被动回复消息图文
     * This model is mapped to the table iqyweixin_reply_msg_news
     */
    public function getSource()
    {
        return 'iqyweixin_reply_msg_news';
    }
}
