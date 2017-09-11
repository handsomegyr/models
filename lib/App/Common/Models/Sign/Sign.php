<?php
namespace App\Common\Models\Sign;
use App\Common\Models\Base\Base; 
class Sign extends Base
{
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Sign\Mysql\Sign());
    }
}