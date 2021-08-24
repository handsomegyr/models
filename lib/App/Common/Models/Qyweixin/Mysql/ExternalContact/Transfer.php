<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class Transfer extends Base
{
    /**
     * 企业微信-外部联系人管理-离职成员的外部联系人再分配
     * This model is mapped to the table iqyweixin_externalcontact_transfer
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_transfer';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['transfer_time'] = $this->changeToValidDate($data['transfer_time']);

        return $data;
    }
}
