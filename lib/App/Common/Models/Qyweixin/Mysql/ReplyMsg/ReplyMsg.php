<?php

namespace App\Common\Models\Qyweixin\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class ReplyMsg extends Base
{
    /**
     * 企业微信-被动回复消息
     * This model is mapped to the table iqyweixin_reply_msg
     */
    public function getSource()
    {
        return 'iqyweixin_reply_msg';
    }
}
