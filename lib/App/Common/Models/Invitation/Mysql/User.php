<?php

namespace App\Common\Models\Invitation\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * 微信邀请-用户表管理
     * This model is mapped to the table iinvitation_user
     */
    public function getSource()
    {
        return 'iinvitation_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        $data['expire'] = $this->changeToValidDate($data['expire']);
        $data['lock'] = $this->changeToBoolean($data['lock']);
        return $data;
    }
}
