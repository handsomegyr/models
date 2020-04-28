<?php

namespace App\Common\Models\Qyweixin\Mysql\Provider;

use App\Common\Models\Base\Mysql\Base;

class ProviderLoginBindTracking extends Base
{
    /**
     * 企业微信-登录授权发起执行时间跟踪统计
     * This model is mapped to the table iqyweixin_provider_login_bind_tracking
     */
    public function getSource()
    {
        return 'iqyweixin_provider_login_bind_tracking';
    }
}
