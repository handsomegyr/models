<?php

namespace App\Common\Models\Backend\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * This model is mapped to the table ibackend_user
     */
    public function getSource()
    {
        return 'ibackend_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['lasttime'] = $this->changeToValidDate($data['lasttime']);
        return $data;
    }
}
