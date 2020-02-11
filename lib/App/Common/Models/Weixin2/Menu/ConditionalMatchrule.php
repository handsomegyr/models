<?php

namespace App\Common\Models\Weixin2\Menu;

use App\Common\Models\Base\Base;

class ConditionalMatchrule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Menu\ConditionalMatchrule());
    }
}
