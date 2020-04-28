<?php

namespace App\Common\Models\Qyweixin\Msg;

use App\Common\Models\Base\Base;

class Log extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Msg\Log());
    }
}
