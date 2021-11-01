<?php

namespace App\Common\Models\Lexiangla\Contact;

use App\Common\Models\Base\Base;

class TagUser extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Lexiangla\Mysql\Contact\TagUser());
    }
}
