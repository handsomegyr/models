<?php

namespace App\Common\Models\Weixin2\Mysql\Component;

use App\Common\Models\Base\Mysql\Base;

class ComponentLoginBindTracking extends Base
{
    /**
     * 微信-登录授权发起执行时间跟踪统计
     * This model is mapped to the table iweixin2_component_login_bind_tracking
     */
    public function getSource()
    {
        return 'iweixin2_component_login_bind_tracking';
    }
}
