<?php

namespace App\Common\Models\Weixin2\Mysql\Miniprogram\Qrcode;

use App\Common\Models\Base\Mysql\Base;

class Type extends Base
{
    /**
     * 微信-小程序二维码类型
     * This model is mapped to the table iweixin2_Miniprogram_qrcode_type
     */
    public function getSource()
    {
        return 'iweixin2_Miniprogram_qrcode_type';
    }
}
