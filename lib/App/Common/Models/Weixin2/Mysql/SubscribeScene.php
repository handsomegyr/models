<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class SubscribeScene extends Base
{
    /**
     * 微信-用户关注渠道来源
     * This model is mapped to the table iweixin2_subscribe_scene
     */
    public function getSource()
    {
        return 'iweixin2_subscribe_scene';
    }
}
