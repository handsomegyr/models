<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class UserActiveStat extends Base
{
    /**
     * 企业微信-通讯录管理-企业活跃成员数
     * This model is mapped to the table iqyweixin_user_active_stat
     */
    public function getSource()
    {
        return 'iqyweixin_user_active_stat';
    }
}
