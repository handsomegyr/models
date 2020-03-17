<?php

namespace App\Common\Models\Live;

use App\Common\Models\Base\Base;

class Room extends Base
{

    use \App\Common\Models\Live\Redis;

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Live\Mysql\Room());
        $this->redis = $this->getDI()->get('redis');
    }

    public function getUploadPath()
    {
        return trim("live/room", '/');
    }
}
