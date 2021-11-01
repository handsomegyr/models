<?php

namespace App\Common\Models\Task;

use App\Common\Models\Base\Base;

class Task extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Task\Mysql\Task());
    }
}
