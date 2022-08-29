<?php

namespace App\Common\Models\Qyweixin\Auth;

use App\Common\Models\Base\Base;

class AuthCorp extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Auth\AuthCorp());
    }
}
