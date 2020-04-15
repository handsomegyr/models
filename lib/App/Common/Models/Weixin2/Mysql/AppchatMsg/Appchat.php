<?php

namespace App\Common\Models\Weixin2\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class Appchat extends Base
{
    /**
     * 微信-群聊会话
     * This model is mapped to the table iweixin2_appchat
     */
    public function getSource()
    {
        return 'iweixin2_appchat';
    }
}
