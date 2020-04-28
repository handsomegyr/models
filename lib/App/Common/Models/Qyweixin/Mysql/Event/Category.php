<?php

namespace App\Common\Models\Qyweixin\Mysql\Event;

use App\Common\Models\Base\Mysql\Base;

class Category extends Base
{
    /**
     * 企业微信-事件分类
     * This model is mapped to the table iqyweixin_event_category
     */
    public function getSource()
    {
        return 'iqyweixin_event_category';
    }
}
