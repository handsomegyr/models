<?php

namespace App\Common\Models\Weixin2\Kf;

use App\Common\Models\Base\Base;

class Session extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Kf\Session());
    }
}
