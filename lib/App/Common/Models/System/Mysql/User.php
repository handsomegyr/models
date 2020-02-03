<?php

namespace App\Common\Models\System\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * This model is mapped to the table user
     */
    public function getSource()
    {
        return 'user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['lasttime'] = $this->changeToMongoDate($data['lasttime']);
        return $data;
    }
}
