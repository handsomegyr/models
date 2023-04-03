<?php

namespace App\Common\Models\Weixin2\Draft;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Draft\News());
    }
}
