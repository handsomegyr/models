<?php

namespace App\Common\Models\Qyweixin\MsgAudit;

use App\Common\Models\Base\Base;

class Chatdata extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\MsgAudit\Chatdata());
    }
}
