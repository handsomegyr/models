<?php

namespace App\Common\Models\Weixin2\CustomMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\CustomMsg\News());
    }

    public function getUploadPath()
    {
        return trim("weixin/custommsg/news", '/');
    }
}
