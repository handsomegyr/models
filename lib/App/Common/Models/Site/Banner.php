<?php

namespace App\Common\Models\Site;

use App\Common\Models\Base\Base;

class Banner extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Site\Mysql\Banner());
    }

    public function getUploadPath()
    {
        return trim("banner", '/');
    }
}
