<?php

namespace App\Common\Models\Weixin2;

use App\Common\Models\Base\Base;

class Callbackurls extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Callbackurls());
    }
}
