<?php

namespace App\Common\Models\Weixin2\Mysql\TemplateMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-模板消息发送日志
     * This model is mapped to the table iweixin2_template_msg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_template_msg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
