<?php

namespace App\Common\Models\Qyweixin\ExternalContact;

use App\Common\Models\Base\Base;

class ResignedTransferResult extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Qyweixin\Mysql\ExternalContact\ResignedTransferResult());
    }
}
