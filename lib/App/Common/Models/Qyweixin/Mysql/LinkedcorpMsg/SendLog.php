<?php

namespace App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 企业微信-互联企业消息发送日志
     * This model is mapped to the table iqyweixin_linkedcorp_msg_send_log
     */
    public function getSource()
    {
        return 'iqyweixin_linkedcorp_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['content_item'] = $this->changeToArray($data['content_item']);
        $data['emphasis_first_item'] = $this->changeToBoolean($data['emphasis_first_item']);
        $data['toall'] = $this->changeToBoolean($data['toall']);
        $data['safe'] = $this->changeToBoolean($data['safe']);
        $data['linkedcorp_msg_content'] = $this->changeToArray($data['linkedcorp_msg_content']);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
