<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class Addway extends Base
{
    /**
     * 企业微信-外部联系人管理-客户来源
     * This model is mapped to the table iqyweixin_externalcontact_add_way
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_add_way';
    }
}
