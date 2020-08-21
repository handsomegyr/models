<?php

namespace App\Common\Models\Database\Project\Collection;

use App\Common\Models\Base\Base;

class Permission extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\Project\Collection\Permission());
    }
}
