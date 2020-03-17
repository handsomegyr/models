<?php

namespace App\Common\Models\Goods;

use App\Common\Models\Base\Base;

class Images extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\Images());
    }

    public function getUploadPath()
    {
        return trim("goods/goods", '/');
    }
}
