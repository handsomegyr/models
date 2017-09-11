<?php
namespace App\Common\Models\Sign;
use App\Common\Models\Base\Base; 
class Log extends Base
{
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Sign\Mysql\Log());
    }
}