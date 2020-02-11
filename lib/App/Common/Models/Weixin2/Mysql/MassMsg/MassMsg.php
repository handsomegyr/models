<?php

namespace App\Common\Models\Weixin2\Mysql\MassMsg;

use App\Common\Models\Base\Mysql\Base;

class MassMsg extends Base
{
    /**
     * 微信-群发消息
     * This model is mapped to the table iweixin2_mass_msg
     */
    public function getSource()
    {
        return 'iweixin2_mass_msg';
    }
}
