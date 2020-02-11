<?php
namespace App\Common\Models\Weixin2\Material;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Material\News());
    }
}