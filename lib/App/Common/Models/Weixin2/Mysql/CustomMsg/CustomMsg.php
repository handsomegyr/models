<?php

namespace App\Common\Models\Weixin2\Mysql\CustomMsg;

use App\Common\Models\Base\Mysql\Base;

class CustomMsg extends Base
{
    /**
     * 微信-客服消息
     * This model is mapped to the table iweixin2_custom_msg
     */
    public function getSource()
    {
        return 'iweixin2_custom_msg';
    }
}
