<?php

namespace App\Common\Models\Weixin2\DataCube;

use App\Common\Models\Base\Base;

class UpstreamMsgDistHour extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\DataCube\UpstreamMsgDistHour());
    }
}
