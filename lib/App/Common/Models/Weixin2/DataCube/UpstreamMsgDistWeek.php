<?php

namespace App\Common\Models\Weixin2\DataCube;

use App\Common\Models\Base\Base;

class UpstreamMsgDistWeek extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\DataCube\UpstreamMsgDistWeek());
    }
}
