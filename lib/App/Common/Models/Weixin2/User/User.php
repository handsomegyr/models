<?php

namespace App\Common\Models\Weixin2\User;

use App\Common\Models\Base\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\User\User());
    }
}
