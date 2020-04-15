<?php

namespace App\Common\Models\Weixin2\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-群聊会话消息发送日志
     * This model is mapped to the table iweixin2_appchat_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_appchat_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['safe'] = $this->changeToBoolean($data['safe']);
        $data['appchat_msg_content'] = $this->changeToArray($data['appchat_msg_content']);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
