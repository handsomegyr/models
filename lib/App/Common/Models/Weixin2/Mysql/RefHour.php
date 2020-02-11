<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class RefHour extends Base
{

    /**
     * 微信-数据分时
     * This model is mapped to the table iweixin2_ref_hour
     */
    public function getSource()
    {
        return 'iweixin2_ref_hour';
    }
}
