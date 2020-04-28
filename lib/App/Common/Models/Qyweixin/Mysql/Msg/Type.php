<?php

namespace App\Common\Models\Qyweixin\Mysql\Msg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-消息类型
     * This model is mapped to the table iqyweixin_msg_type
     */
    public function getSource()
    {
        return 'iqyweixin_msg_type';
    }
}
