<?php

namespace App\Common\Models\Weixin2\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class BlackUser extends Base
{
    /**
     * 微信-黑名单
     * This model is mapped to the table iweixin2_black_user
     */
    public function getSource()
    {
        return 'iweixin2_black_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['black_time'] = $this->changeToMongoDate($data['black_time']);
        $data['unblack_time'] = $this->changeToMongoDate($data['unblack_time']);

        $data['is_black'] = $this->changeToBoolean($data['is_black']);
        return $data;
    }
}
