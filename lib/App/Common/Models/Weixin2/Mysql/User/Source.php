<?php

namespace App\Common\Models\Weixin2\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class Source extends Base
{
    /**
     * 微信-用户渠道来源
     * This model is mapped to the table iweixin2_user_source
     */
    public function getSource()
    {
        return 'iweixin2_user_source';
    }
}
