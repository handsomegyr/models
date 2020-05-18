<?php

namespace App\Common\Models\Company;

use App\Common\Models\Base\Base;

class Service extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Company\Mysql\Service());
    }
}
