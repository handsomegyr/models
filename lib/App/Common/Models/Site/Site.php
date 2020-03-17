<?php

namespace App\Common\Models\Site;

use App\Common\Models\Base\Base;

class Site extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Site\Mysql\Site());
    }

    public function getUploadPath()
    {
        return trim("site/site", '/');
    }
}
