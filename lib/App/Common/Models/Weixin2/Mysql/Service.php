<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Service extends Base
{
    /**
     * 微信-服务
     * This model is mapped to the table iweixin2_service
     */
    public function getSource()
    {
        return 'iweixin2_service';
    }
}
