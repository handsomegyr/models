<?php

namespace App\Common\Models\Qyweixin\User;

use App\Common\Models\Base\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\User\User());
    }
}
