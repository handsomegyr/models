<?php
namespace App\Common\Models\Message;

use App\Common\Models\Base\Base;

class MsgCount extends Base
{

    function __construct($db = Base::MYSQL)
    {
        $className = $this->getClassNameByDb($db, '\App\Common\Models\Message\%s\MsgCount');
        $this->setModel(new $className());
    }
}