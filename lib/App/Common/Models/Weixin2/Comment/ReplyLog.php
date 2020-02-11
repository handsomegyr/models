<?php

namespace App\Common\Models\Weixin2\Comment;

use App\Common\Models\Base\Base;

class ReplyLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Comment\ReplyLog());
    }
}
