<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram\Qrcode;

use App\Common\Models\Base\Mysql\Base;

class Qrcode extends Base
{

    /**
     * 微信-小程序二维码
     * This model is mapped to the table iweixin2_miniprogram_qrcode
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_qrcode';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['qrcode_time'] = $this->changeToMongoDate($data['qrcode_time']);
        $data['line_color'] = $this->changeToArray($data['line_color']);
        $data['auto_color'] = $this->changeToBoolean($data['auto_color']);
        $data['is_hyaline'] = $this->changeToBoolean($data['is_hyaline']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
