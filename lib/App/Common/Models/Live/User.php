<?php
namespace App\Common\Models\Live;

use App\Common\Models\Base\Base;

class User extends Base
{
    use \App\Common\Models\Live\Redis;

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Live\Mysql\User());
        $this->redis = $this->getDI()->get('redis');
    }
}