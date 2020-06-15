<?php

namespace App\Common\Models\Database\Plugin\Collection;

use App\Common\Models\Base\Base;

class Data extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\Plugin\Collection\Data());
    }
}
