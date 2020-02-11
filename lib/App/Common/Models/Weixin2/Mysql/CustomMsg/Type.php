<?php

namespace App\Common\Models\Weixin2\Mysql\CustomMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-客服消息类型
     * This model is mapped to the table iweixin2_custom_msg_type
     */
    public function getSource()
    {
        return 'iweixin2_custom_msg_type';
    }
}
