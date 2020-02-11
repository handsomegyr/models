<?php

namespace App\Common\Models\Weixin2\Mysql\CustomMsg;

use App\Common\Models\Base\Mysql\Base;

class News extends Base
{
    /**
     * 微信-客服消息图文
     * This model is mapped to the table iweixin2_custom_msg_news
     */
    public function getSource()
    {
        return 'iweixin2_custom_msg_news';
    }
}
