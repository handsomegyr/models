<?php

namespace App\Common\Models\Backend;

use App\Common\Models\Base\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Backend\Mysql\User());
    }

    public function getUploadPath()
    {
        return trim("backend/user", '/');
    }
}
