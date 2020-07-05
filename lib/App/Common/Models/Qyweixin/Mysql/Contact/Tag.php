<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class Tag extends Base
{
    /**
     * 企业微信-通讯录管理-标签
     * This model is mapped to the table iqyweixin_tag
     */
    public function getSource()
    {
        return 'iqyweixin_tag';
    }
}
