<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupMsg extends Base
{
    /**
     * 企业微信-外部联系人管理-企业群发记录
     * This model is mapped to the table iqyweixin_externalcontact_group_msg
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['create_time'] = $this->changeToValidDate($data['create_time']);
        return $data;
    }
}
