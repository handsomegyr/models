<?php

namespace App\Common\Models\Qyweixin\LinkedcorpMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\LinkedcorpMsg\News());
    }

    public function getUploadPath()
    {
        return trim("qyweixin/linkedcorpmsg/news", '/');
    }
}
