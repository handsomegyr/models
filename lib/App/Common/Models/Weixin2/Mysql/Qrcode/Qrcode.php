<?php

namespace App\Common\Models\Weixin2\Mysql\Qrcode;

use App\Common\Models\Base\Mysql\Base;

class Qrcode extends Base
{
    /**
     * 微信-二维码
     * This model is mapped to the table iweixin2_qrcode
     */
    public function getSource()
    {
        return 'iweixin2_qrcode';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['ticket_time'] = $this->changeToMongoDate($data['ticket_time']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
