<?php

namespace App\Common\Models\Weixin2\Mysql\Event;

use App\Common\Models\Base\Mysql\Base;

class Category extends Base
{
    /**
     * 微信-事件分类
     * This model is mapped to the table iweixin2_event_category
     */
    public function getSource()
    {
        return 'iweixin2_event_category';
    }
}
