<?php

namespace App\Common\Models\Qyweixin\Mysql\MsgAudit;

use App\Common\Models\Base\Mysql\Base;

class Sn extends Base
{
    /**
     * 企业微信-会话内容存档-密钥
     * This model is mapped to the table iqyweixin_msgaudit_sn
     */
    public function getSource()
    {
        return 'iqyweixin_msgaudit_sn';
    }
}
