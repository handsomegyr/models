<?php

namespace App\Common\Models\Weixin2\Miniprogram;

use App\Common\Models\Base\Base;

class Urlscheme extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Miniprogram\Urlscheme());
    }
}
