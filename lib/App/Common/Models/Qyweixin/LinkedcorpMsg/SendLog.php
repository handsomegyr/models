<?php

namespace App\Common\Models\Qyweixin\LinkedcorpMsg;

use App\Common\Models\Base\Base;

class SendLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg\SendLog());
    }
}
