<?php

namespace App\Common\Models\Weixin2\ReplyMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\ReplyMsg\News());
    }
}
