<?php
namespace App\Common\Models\Weixin2\Keyword;

use App\Common\Models\Base\Base;

class KeywordToService extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\Keyword\KeywordToService());
    }
    
}