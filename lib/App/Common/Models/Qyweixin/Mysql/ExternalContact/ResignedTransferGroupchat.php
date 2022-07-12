<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ResignedTransferGroupchat extends Base
{
    /**
     * 企业微信-外部联系人管理-分配离职成员的客户群
     * This model is mapped to the table iqyweixin_externalcontact_resigned_transfer_groupchat
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_resigned_transfer_groupchat';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['transfer_time'] = $this->changeToValidDate($data['transfer_time']);
        $data['is_transfered'] = $this->changeToBoolean($data['is_transfered']);
        return $data;
    }
}
