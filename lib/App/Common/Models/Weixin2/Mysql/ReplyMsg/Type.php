<?php

namespace App\Common\Models\Weixin2\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-被动回复消息类型
     * This model is mapped to the table iweixin2_reply_msg_type
     */
    public function getSource()
    {
        return 'iweixin2_reply_msg_type';
    }
}
