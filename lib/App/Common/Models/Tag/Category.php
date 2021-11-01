<?php

namespace App\Common\Models\Tag;

use App\Common\Models\Base\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Tag\Mysql\Category());
    }
}
