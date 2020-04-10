<?php

namespace App\Common\Models\Weixin2\AgentMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\AgentMsg\News());
    }

    public function getUploadPath()
    {
        return trim("weixin2/agentmsg/news", '/');
    }
}
