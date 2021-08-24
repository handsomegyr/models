<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram\SubscribeMsg;

use App\Common\Models\Base\Mysql\Base;

class SendLog extends Base
{
    /**
     * 微信-小程序订阅消息发送日志
     * This model is mapped to the table iweixin2_miniprogram_subscribemsg_send_log
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_subscribemsg_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['subscribemsg_content'] = $this->changeToArray($data['subscribemsg_content']);
        $data['data'] = $this->changeToArray($data['data']);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
