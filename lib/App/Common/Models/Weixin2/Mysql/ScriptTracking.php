<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ScriptTracking extends Base
{
    /**
     * 微信-授权执行时间跟踪统计
     * This model is mapped to the table iweixin2_script_tracking
     */
    public function getSource()
    {
        return 'iweixin2_script_tracking';
    }
}
