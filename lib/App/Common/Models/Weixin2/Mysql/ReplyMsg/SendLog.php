<?php

namespace App\Common\Models\Weixin2\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-被动回复消息发送日志
     * This model is mapped to the table iweixin2_reply_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_reply_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
