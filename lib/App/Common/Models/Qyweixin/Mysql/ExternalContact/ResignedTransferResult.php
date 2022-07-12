<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ResignedTransferResult extends Base
{
    /**
     * 企业微信-外部联系人管理-离职成员的客户分配情况
     * This model is mapped to the table iqyweixin_externalcontact_resigned_transfer_result
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_resigned_transfer_result';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['takeover_time'] = $this->changeToValidDate($data['takeover_time']);
        return $data;
    }
}
