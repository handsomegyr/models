<?php

namespace App\Common\Models\Weixin2\FreePublish;

use App\Common\Models\Base\Base;

class Article extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\FreePublish\Article());
    }
}
