<?php

namespace App\Common\Models\Database\Project;

use App\Common\Models\Base\Base;

class Collection extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\Project\Collection());
    }
}
