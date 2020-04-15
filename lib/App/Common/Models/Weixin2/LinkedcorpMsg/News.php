<?php

namespace App\Common\Models\Weixin2\LinkedcorpMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\LinkedcorpMsg\News());
    }

    public function getUploadPath()
    {
        return trim("weixin2/linkedcorpmsg/news", '/');
    }
}
