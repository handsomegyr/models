<?php

namespace App\Common\Models\Qyweixin\Notification;

use App\Common\Models\Base\Base;

class TaskLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Notification\TaskLog());
    }
}
