<?php

namespace App\Common\Models\Qyweixin\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 企业微信-群聊会话消息类型
     * This model is mapped to the table iqyweixin_appchat_msg_type
     */
    public function getSource()
    {
        return 'iqyweixin_appchat_msg_type';
    }
}
