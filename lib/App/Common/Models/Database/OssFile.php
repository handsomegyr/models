<?php

namespace App\Common\Models\Database;

use App\Common\Models\Base\Base;

class OssFile extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\OssFile());
    }
}
