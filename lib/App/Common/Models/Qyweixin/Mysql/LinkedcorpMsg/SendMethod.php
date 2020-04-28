<?php

namespace App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class SendMethod extends Base
{
    /**
     * 企业微信-互联企业消息发送方式
     * This model is mapped to the table iqyweixin_linkedcorp_msg_send_method
     */
    public function getSource()
    {
        return 'iqyweixin_linkedcorp_msg_send_method';
    }
}
