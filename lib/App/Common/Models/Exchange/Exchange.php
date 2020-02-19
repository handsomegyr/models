<?php

namespace App\Common\Models\Exchange;

use App\Common\Models\Base\Base;

class Exchange extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Exchange\Mysql\Exchange());
    }
}
