<?php

namespace App\Common\Models\Qyweixin\AgentMsg;

use App\Common\Models\Base\Base;

class News extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\AgentMsg\News());
    }

    public function getUploadPath()
    {
        return trim("qyweixin/agentmsg/news", '/');
    }
}
