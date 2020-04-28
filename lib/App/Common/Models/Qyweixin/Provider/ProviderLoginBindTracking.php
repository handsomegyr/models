<?php

namespace App\Common\Models\Qyweixin\Provider;

use App\Common\Models\Base\Base;

class ProviderLoginBindTracking extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Provider\ProviderLoginBindTracking());
    }
}
