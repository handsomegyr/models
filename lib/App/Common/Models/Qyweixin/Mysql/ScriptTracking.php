<?php

namespace App\Common\Models\Qyweixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ScriptTracking extends Base
{
    /**
     * 企业微信-授权执行时间跟踪统计
     * This model is mapped to the table iqyweixin_script_tracking
     */
    public function getSource()
    {
        return 'iqyweixin_script_tracking';
    }
}
