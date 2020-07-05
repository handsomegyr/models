<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class DepartmentUser extends Base
{
    /**
     * 企业微信-通讯录管理-部门成员
     * This model is mapped to the table iqyweixin_department_user
     */
    public function getSource()
    {
        return 'iqyweixin_department_user';
    }
}
