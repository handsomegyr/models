<?php

namespace App\Common\Models\Qyweixin\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-被动回复消息类型
     * This model is mapped to the table iqyweixin_reply_msg_type
     */
    public function getSource()
    {
        return 'iqyweixin_reply_msg_type';
    }
}
