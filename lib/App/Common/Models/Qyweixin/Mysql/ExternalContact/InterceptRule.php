<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class InterceptRule extends Base
{
    /**
     * 企业微信-外部联系人管理-聊天敏感词
     * This model is mapped to the table iqyweixin_externalcontact_intercept_rule
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_intercept_rule';
    }
}
