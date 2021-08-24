<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram\Qrcode;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{
    /**
     * 微信-小程序二维码扫描日志
     * This model is mapped to the table iweixin2_miniprogram_qrcode_log
     */
    public function getSource()
    {
        return 'iweixin2_miniprogram_qrcode_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
