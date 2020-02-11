<?php
namespace App\Common\Models\Weixin2\TemplateMsg;

use App\Common\Models\Base\Base;

class SendLog extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin2\Mysql\TemplateMsg\SendLog());
    }
}