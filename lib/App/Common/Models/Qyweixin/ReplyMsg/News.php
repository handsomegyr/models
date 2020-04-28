<?php

namespace App\Common\Models\Qyweixin\ReplyMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\ReplyMsg\News());
    }

    public function getUploadPath()
    {
        return trim("qyweixin/replymsg/news", '/');
    }
}
