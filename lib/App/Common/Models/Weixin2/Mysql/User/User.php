<?php

namespace App\Common\Models\Weixin2\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{
    /**
     * 微信-用户
     * This model is mapped to the table iweixin2_user
     */
    public function getSource()
    {
        return 'iweixin2_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['access_token'] = $this->changeToArray($data['access_token']);
        $data['tagid_list'] = $this->changeToArray($data['tagid_list']);
        $data['privilege'] = $this->changeToArray($data['privilege']);
        $data['subscribe_time'] = $this->changeToMongoDate($data['subscribe_time']);
        $data['subscribe'] = $this->changeToBoolean($data['subscribe']);
        return $data;
    }
}
