<?php

namespace App\Common\Models\Lexiangla\Contact;

use App\Common\Models\Base\Base;

class TagSync extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Lexiangla\Mysql\Contact\TagSync());
    }
}
