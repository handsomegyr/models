<?php

namespace App\Common\Models\Banner;

use App\Common\Models\Base\Base;

class Item extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Banner\Mysql\Item());
    }
}
