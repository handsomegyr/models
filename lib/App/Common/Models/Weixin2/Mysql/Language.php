<?php

namespace App\Common\Models\Weixin2\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Language extends Base
{
    /**
     * 微信-语言
     * This model is mapped to the table iweixin2_language
     */
    public function getSource()
    {
        return 'iweixin2_language';
    }
}
