<?php

namespace App\Common\Models\Backend\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Resource extends Base
{

    /**
     * This model is mapped to the table ibackend_resource
     */
    public function getSource()
    {
        return 'ibackend_resource';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}
