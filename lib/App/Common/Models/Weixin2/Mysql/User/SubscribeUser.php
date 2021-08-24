<?php

namespace App\Common\Models\Weixin2\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class SubscribeUser extends Base
{
    /**
     * 微信-关注用户
     * This model is mapped to the table iweixin2_subscribe_user
     */
    public function getSource()
    {
        return 'iweixin2_subscribe_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        return $data;
    }
}
