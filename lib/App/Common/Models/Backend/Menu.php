<?php

namespace App\Common\Models\Backend;

use App\Common\Models\Base\Base;

class Menu extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Backend\Mysql\Menu());
    }
}
