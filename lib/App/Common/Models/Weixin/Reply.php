<?php

namespace App\Common\Models\Weixin;

use App\Common\Models\Base\Base;

class Reply extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\Reply());
    }

    public function getUploadPath()
    {
        return trim("weixin/reply", '/');
    }
}
