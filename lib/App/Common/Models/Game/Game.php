<?php

namespace App\Common\Models\Game;

use App\Common\Models\Base\Base;

class Game extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Game\Mysql\Game());
    }
}
