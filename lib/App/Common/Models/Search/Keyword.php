<?php

namespace App\Common\Models\Search;

use App\Common\Models\Base\Base;

class Keyword extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Search\Mysql\Keyword());
    }
}
