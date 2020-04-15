<?php

namespace App\Common\Models\Weixin2\AppchatMsg;

use App\Common\Models\Base\Base;

class SendLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\AppchatMsg\SendLog());
    }
}
