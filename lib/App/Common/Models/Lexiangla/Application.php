<?php

namespace App\Common\Models\Lexiangla;

use App\Common\Models\Base\Base;

class Application extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Lexiangla\Mysql\Application());
    }
}
