<?php

namespace App\Common\Models\Weixin2\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 微信-被动回复消息图文
     * This model is mapped to the table iweixin2_reply_msg_news
     */
    public function getSource()
    {
        return 'iweixin2_reply_msg_news';
    }
}
