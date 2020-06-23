<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class Unassigned extends Base
{
    /**
     * 企业微信-外部联系人管理-离职成员的客户
     * This model is mapped to the table iqyweixin_externalcontact_unassigned
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_unassigned';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['dimission_time'] = $this->changeToMongoDate($data['dimission_time']);
        $data['sync_time'] = $this->changeToMongoDate($data['sync_time']);
        return $data;
    }
}
