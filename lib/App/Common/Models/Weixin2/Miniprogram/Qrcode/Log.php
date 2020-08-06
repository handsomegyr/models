<?php

namespace App\Common\Models\Weixin2\Miniprogram\Qrcode;

use App\Common\Models\Base\Base;

class Log extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Miniprogram\Qrcode\Log());
    }
}
