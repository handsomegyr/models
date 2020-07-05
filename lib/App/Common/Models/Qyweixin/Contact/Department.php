<?php

namespace App\Common\Models\Qyweixin\Contact;

use App\Common\Models\Base\Base;

class Department extends  Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Contact\Department());
    }
}
