<?php

namespace App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-互联企业消息类型
     * This model is mapped to the table iqyweixin_linkedcorp_msg_type
     */
    public function getSource()
    {
        return 'iqyweixin_linkedcorp_msg_type';
    }
}
