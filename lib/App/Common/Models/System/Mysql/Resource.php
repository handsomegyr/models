<?php
namespace App\Common\Models\System\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Resource extends Base
{

    /**
     * This model is mapped to the table resource
     */
    public function getSource()
    {
        return 'resource';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}