<?php

namespace App\Common\Models\Weixin2\SubscribeMsg;

use App\Common\Models\Base\Base;

class SubscribeLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\SubscribeMsg\SubscribeLog());
    }
}
