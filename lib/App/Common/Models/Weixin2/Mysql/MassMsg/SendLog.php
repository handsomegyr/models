<?php

namespace App\Common\Models\Weixin2\Mysql\MassMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-群发消息发送日志
     * This model is mapped to the table iweixin2_mass_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_mass_msg_send_log';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        $data['msg_time'] = $this->changeToValidDate($data['msg_time']);

        $data['send_ignore_reprint'] = $this->changeToBoolean($data['send_ignore_reprint']);
        $data['is_to_all'] = $this->changeToBoolean($data['is_to_all']);

        return $data;
    }
}
