<?php

namespace App\Common\Models\Weixin2\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-群聊会话消息类型
     * This model is mapped to the table iweixin2_appchat_msg_type
     */
    public function getSource()
    {
        return 'iweixin2_appchat_msg_type';
    }
}
