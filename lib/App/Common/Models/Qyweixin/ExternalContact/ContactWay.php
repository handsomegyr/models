<?php

namespace App\Common\Models\Qyweixin\ExternalContact;

use App\Common\Models\Base\Base;

class ContactWay extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\ExternalContact\ContactWay());
    }
}
