<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class MomentCustomer extends Base
{
    /**
     * 企业微信-外部联系人管理-客户朋友圈发表时选择的可见范围
     * This model is mapped to the table iqyweixin_externalcontact_moment_task_customer
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_moment_task_customer';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        return $data;
    }
}
