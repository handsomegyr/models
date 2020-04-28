<?php

namespace App\Common\Models\Qyweixin\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class Appchat extends Base
{
    /**
     * 企业微信-群聊会话
     * This model is mapped to the table iqyweixin_appchat
     */
    public function getSource()
    {
        return 'iqyweixin_appchat';
    }
}
