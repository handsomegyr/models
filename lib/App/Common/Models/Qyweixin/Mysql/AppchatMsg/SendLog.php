<?php

namespace App\Common\Models\Qyweixin\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 企业微信-群聊会话消息发送日志
     * This model is mapped to the table iqyweixin_appchat_msg_send_log
     */
    public function getSource()
    {
        return 'iqyweixin_appchat_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['safe'] = $this->changeToBoolean($data['safe']);
        $data['appchat_msg_content'] = $this->changeToArray($data['appchat_msg_content']);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
