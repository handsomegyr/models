<?php

namespace App\Common\Models\Company;

use App\Common\Models\Base\Base;

class Project extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Company\Mysql\Project());
    }

    public function getUploadPath()
    {
        return trim("company/project", '/');
    }
}
