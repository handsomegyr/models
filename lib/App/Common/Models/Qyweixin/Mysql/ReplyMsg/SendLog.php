<?php

namespace App\Common\Models\Qyweixin\Mysql\ReplyMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 企业微信-被动回复消息发送日志
     * This model is mapped to the table iqyweixin_reply_msg_send_log
     */
    public function getSource()
    {
        return 'iqyweixin_reply_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
