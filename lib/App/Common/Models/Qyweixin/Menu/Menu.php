<?php

namespace App\Common\Models\Qyweixin\Menu;

use App\Common\Models\Base\Base;

class Menu extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Menu\Menu());
    }
}
