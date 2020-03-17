<?php

namespace App\Common\Models\Banner;

use App\Common\Models\Base\Base;

class Banner extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Banner\Mysql\Banner());
    }

    public function getUploadPath()
    {
        return trim("banner/banner", '/');
    }
}
