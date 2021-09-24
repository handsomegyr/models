<?php

namespace App\Common\Models\Qyweixin\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{
    /**
     * 企业微信-企业用户
     * This model is mapped to the table iqyweixin_user
     */
    public function getSource()
    {
        return 'iqyweixin_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token'] = $this->changeToArray($data['access_token']);
        $data['department'] = $this->changeToArray($data['department']);
        $data['department_order'] = $this->changeToArray($data['department_order']);
        $data['is_leader_in_dept'] = $this->changeToArray($data['is_leader_in_dept']);
        $data['extattr'] = $this->changeToArray($data['extattr']);
        $data['external_profile'] = $this->changeToArray($data['external_profile']);
        $data['external_position'] = $this->changeToArray($data['external_position']);
        return $data;
    }
}
