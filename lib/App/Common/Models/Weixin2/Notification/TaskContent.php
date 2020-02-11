<?php

namespace App\Common\Models\Weixin2\Notification;

use App\Common\Models\Base\Base;

class TaskContent extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Notification\TaskContent());
    }
}
