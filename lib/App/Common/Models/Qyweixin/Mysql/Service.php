<?php

namespace App\Common\Models\Qyweixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Service extends Base
{
    /**
     * 企业微信-服务
     * This model is mapped to the table iqyweixin_service
     */
    public function getSource()
    {
        return 'iqyweixin_service';
    }
}
