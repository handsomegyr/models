<?php

namespace App\Common\Models\Backend;

use App\Common\Models\Base\Base;

class Role extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Backend\Mysql\Role());
    }
}
