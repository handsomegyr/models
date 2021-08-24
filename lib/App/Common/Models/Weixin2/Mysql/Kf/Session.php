<?php

namespace App\Common\Models\Weixin2\Mysql\Kf;

use App\Common\Models\Base\Mysql\Base;

class Session extends Base
{
    /**
     * 微信-客服会话
     * This model is mapped to the table iweixin2_kfsession
     */
    public function getSource()
    {
        return 'iweixin2_kfsession';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['kfsession_time'] = $this->changeToValidDate($data['kfsession_time']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
