<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class CorpJoinQrcode extends Base
{
    /**
     * 企业微信-通讯录管理-加入企业二维码
     * This model is mapped to the table iqyweixin_corp_join_qrcode
     */
    public function getSource()
    {
        return 'iqyweixin_corp_join_qrcode';
    }
}
