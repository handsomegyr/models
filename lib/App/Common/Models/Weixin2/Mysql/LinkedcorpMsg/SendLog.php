<?php

namespace App\Common\Models\Weixin2\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-互联企业消息发送日志
     * This model is mapped to the table iweixin2_linkedcorp_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_linkedcorp_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['content_item'] = $this->changeToArray($data['content_item']);
        $data['emphasis_first_item'] = $this->changeToBoolean($data['emphasis_first_item']);
        $data['toall'] = $this->changeToBoolean($data['toall']);
        $data['safe'] = $this->changeToBoolean($data['safe']);
        $data['linkedcorp_msg_content'] = $this->changeToArray($data['linkedcorp_msg_content']);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
