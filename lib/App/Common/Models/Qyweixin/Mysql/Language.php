<?php

namespace App\Common\Models\Qyweixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Language extends Base
{
    /**
     * 企业微信-语言
     * This model is mapped to the table iqyweixin_language
     */
    public function getSource()
    {
        return 'iqyweixin_language';
    }
}
