<?php

namespace App\Common\Models\Weixin2\Mysql\MassMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-群发消息类型
     * This model is mapped to the table iweixin2_mass_msg_type
     */
    public function getSource()
    {
        return 'iweixin2_mass_msg_type';
    }
}
