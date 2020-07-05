<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class BatchInvite extends Base
{
    /**
     * 企业微信-通讯录管理-批量邀请成员
     * This model is mapped to the table iqyweixin_batch_invite
     */
    public function getSource()
    {
        return 'iqyweixin_batch_invite';
    }
}
