<?php

namespace App\Common\Models\Weixin2\Mysql\MassMsg;

use App\Common\Models\Base\Mysql\Base;

class SendMethod extends Base
{
    /**
     * 微信-群发消息发送方式
     * This model is mapped to the table iweixin2_mass_msg_send_method
     */
    public function getSource()
    {
        return 'iweixin2_mass_msg_send_method';
    }
}
