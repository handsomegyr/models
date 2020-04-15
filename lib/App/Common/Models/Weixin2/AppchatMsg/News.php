<?php

namespace App\Common\Models\Weixin2\AppchatMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\AppchatMsg\News());
    }

    public function getUploadPath()
    {
        return trim("weixin2/appchatmsg/news", '/');
    }
}
