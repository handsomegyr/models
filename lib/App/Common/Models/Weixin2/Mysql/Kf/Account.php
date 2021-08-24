<?php

namespace App\Common\Models\Weixin2\Mysql\Kf;

use App\Common\Models\Base\Mysql\Base;

class Account extends Base
{
    /**
     * 微信-客服帐号
     * This model is mapped to the table iweixin2_kfaccount
     */
    public function getSource()
    {
        return 'iweixin2_kfaccount';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['kf_time'] = $this->changeToValidDate($data['kf_time']);
        $data['invite_expire_time'] = $this->changeToValidDate($data['invite_expire_time']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
