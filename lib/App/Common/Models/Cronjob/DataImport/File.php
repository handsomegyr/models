<?php

namespace App\Common\Models\Cronjob\DataImport;

use App\Common\Models\Base\Base;

class File extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Cronjob\Mysql\DataImport\File());
    }
}
