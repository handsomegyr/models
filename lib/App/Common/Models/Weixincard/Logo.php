<?php

namespace App\Common\Models\Weixincard;

use App\Common\Models\Base\Base;

class Logo extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixincard\Mysql\Logo());
    }



    public function getUploadPath()
    {
        return trim("weixincard/logo", '/');
    }
}
