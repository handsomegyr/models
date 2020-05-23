<?php

namespace App\Common\Models\Database\Plugin;

use App\Common\Models\Base\Base;

class Collection extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\Plugin\Collection());
    }
}
