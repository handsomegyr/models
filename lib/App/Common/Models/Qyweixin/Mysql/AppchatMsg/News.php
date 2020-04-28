<?php

namespace App\Common\Models\Qyweixin\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 企业微信-群聊会话消息图文
     * This model is mapped to the table iqyweixin_appchat_msg_news
     */
    public function getSource()
    {
        return 'iqyweixin_appchat_msg_news';
    }
}
