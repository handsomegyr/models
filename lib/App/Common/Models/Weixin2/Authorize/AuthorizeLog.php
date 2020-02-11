<?php

namespace App\Common\Models\Weixin2\Authorize;

use App\Common\Models\Base\Base;

class AuthorizeLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Authorize\AuthorizeLog());
    }
}
