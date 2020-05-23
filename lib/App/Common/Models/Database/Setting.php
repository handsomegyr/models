<?php

namespace App\Common\Models\Database;

use App\Common\Models\Base\Base;

class Setting extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\Setting());
    }
}
