<?php

namespace App\Common\Models\Company;

use App\Common\Models\Base\Base;

class Component extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Company\Mysql\Component());
    }

    public function getUploadPath()
    {
        return trim("company/component", '/');
    }
}
