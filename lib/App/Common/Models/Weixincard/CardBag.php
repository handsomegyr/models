<?php

namespace App\Common\Models\Weixincard;

use App\Common\Models\Base\Base;

class CardBag extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixincard\Mysql\CardBag());
    }
}
