<?php

namespace App\Common\Models\Qyweixin\AppchatMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\AppchatMsg\News());
    }

    public function getUploadPath()
    {
        return trim("qyweixin/appchatmsg/news", '/');
    }
}
