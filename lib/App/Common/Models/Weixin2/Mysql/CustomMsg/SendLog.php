<?php

namespace App\Common\Models\Weixin2\Mysql\CustomMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-客服消息发送日志
     * This model is mapped to the table iweixin2_custom_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_custom_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
