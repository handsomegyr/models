<?php

namespace App\Common\Models\Weixin2\Component;

use App\Common\Models\Base\Base;

class ComponentLoginBindTracking extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Component\ComponentLoginBindTracking());
    }
}
