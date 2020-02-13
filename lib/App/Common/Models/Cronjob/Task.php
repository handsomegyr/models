<?php

namespace App\Common\Models\Cronjob;

use App\Common\Models\Base\Base;

class Task extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Cronjob\Mysql\Task());
    }
}
