<?php

namespace App\Common\Models\Tag;

use App\Common\Models\Base\Base;

class TagToEntity extends Base
{
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Tag\Mysql\TagToEntity());
    }
}
