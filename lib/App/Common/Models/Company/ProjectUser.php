<?php

namespace App\Common\Models\Company;

use App\Common\Models\Base\Base;

class ProjectUser extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Company\Mysql\ProjectUser());
    }
}
