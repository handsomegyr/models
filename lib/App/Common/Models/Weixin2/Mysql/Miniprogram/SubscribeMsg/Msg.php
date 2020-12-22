<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram\SubscribeMsg;

use App\Common\Models\Base\Mysql\Base;

class Msg extends Base
{
    /**
     * 微信-小程序订阅消息
     * This model is mapped to the table iweixin2_miniprogram_subscribemsg_msg
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_subscribemsg_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['data'] = $this->changeToArray($data['data']);
        return $data;
    }
}
