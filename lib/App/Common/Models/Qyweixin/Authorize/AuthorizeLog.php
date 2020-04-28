<?php

namespace App\Common\Models\Qyweixin\Authorize;

use App\Common\Models\Base\Base;

class AuthorizeLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Authorize\AuthorizeLog());
    }
}
