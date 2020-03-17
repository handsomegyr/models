<?php

namespace App\Common\Models\Vote;

use App\Common\Models\Base\Base;

class Subject extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Vote\Mysql\Subject());
    }

    public function getUploadPath()
    {
        return trim("vote/subject", '/');
    }
}
