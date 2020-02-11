<?php

namespace App\Common\Models\Weixin2\Mysql\Event;

use App\Common\Models\Base\Mysql\Base;

class Event extends Base
{
    /**
     * 微信-事件
     * This model is mapped to the table iweixin2_event
     */
    public function getSource()
    {
        return 'iweixin2_event';
    }
}
