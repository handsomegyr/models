<?php
namespace App\Common\Models\Qyweixin\Keyword;

use App\Common\Models\Base\Base;

class KeywordToReplyMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\Keyword\KeywordToReplyMsg());
    }
    
}