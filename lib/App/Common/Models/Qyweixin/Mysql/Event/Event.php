<?php

namespace App\Common\Models\Qyweixin\Mysql\Event;

use App\Common\Models\Base\Mysql\Base;

class Event extends Base
{
    /**
     * 企业微信-事件
     * This model is mapped to the table iqyweixin_event
     */
    public function getSource()
    {
        return 'iqyweixin_event';
    }
}
