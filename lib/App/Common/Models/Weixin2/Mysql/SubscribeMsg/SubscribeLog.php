<?php

namespace App\Common\Models\Weixin2\Mysql\SubscribeMsg;

use App\Common\Models\Base\Mysql\Base;

class SubscribeLog extends Base
{
    /**
     * 微信-一次性订阅消息订阅日志
     * This model is mapped to the table iweixin2_subscribe_msg_subscribe_log
     */
    public function getSource()
    {
        return 'iweixin2_subscribe_msg_subscribe_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['subscribe_time'] = $this->changeToMongoDate($data['subscribe_time']);
        $data['used_time'] = $this->changeToMongoDate($data['used_time']);

        $data['is_used'] = $this->changeToBoolean($data['is_used']);
        return $data;
    }
}
