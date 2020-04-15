<?php

namespace App\Common\Models\Weixin2\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class SendMethod extends Base
{
    /**
     * 微信-互联企业消息发送方式
     * This model is mapped to the table iweixin2_linkedcorp_msg_send_method
     */
    public function getSource()
    {
        return 'iweixin2_linkedcorp_msg_send_method';
    }
}
