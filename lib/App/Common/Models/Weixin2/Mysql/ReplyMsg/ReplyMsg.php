<?php

namespace App\Common\Models\Weixin2\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class ReplyMsg extends Base
{
    /**
     * 微信-被动回复消息
     * This model is mapped to the table iweixin2_reply_msg
     */
    public function getSource()
    {
        return 'iweixin2_reply_msg';
    }
}
