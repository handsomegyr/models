<?php

namespace App\Common\Models\Goods;

use App\Common\Models\Base\Base;

class Brand extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\Brand());
    }

    public function getUploadPath()
    {
        return trim("goods/brand", '/');
    }
}
