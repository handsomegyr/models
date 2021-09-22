<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class Department extends Base
{
    /**
     * 企业微信-通讯录管理-部门
     * This model is mapped to the table iqyweixin_department
     */
    public function getSource()
    {
        return 'iqyweixin_department';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_exist'] = $this->changeToBoolean($data['is_exist']);
        return $data;
    }
}
