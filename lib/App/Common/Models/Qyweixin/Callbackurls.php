<?php

namespace App\Common\Models\Qyweixin;

use App\Common\Models\Base\Base;

class Callbackurls extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Callbackurls());
    }
}
