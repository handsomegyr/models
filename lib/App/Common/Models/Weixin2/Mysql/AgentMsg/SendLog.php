<?php

namespace App\Common\Models\Weixin2\Mysql\AgentMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-应用消息发送日志
     * This model is mapped to the table iweixin2_agent_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_agent_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['content_item'] = $this->changeToArray($data['content_item']);
        $data['btn'] = $this->changeToArray($data['btn']);
        $data['emphasis_first_item'] = $this->changeToBoolean($data['emphasis_first_item']);
        $data['safe'] = $this->changeToBoolean($data['safe']);
        $data['enable_id_trans'] = $this->changeToBoolean($data['enable_id_trans']);
        $data['enable_duplicate_check'] = $this->changeToBoolean($data['enable_duplicate_check']);

        $data['agent_msg_content'] = $this->changeToArray($data['agent_msg_content']);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
