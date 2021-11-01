<?php

namespace App\Common\Models\Tag;

use App\Common\Models\Base\Base;

class Tag extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Tag\Mysql\Tag());
    }
}
