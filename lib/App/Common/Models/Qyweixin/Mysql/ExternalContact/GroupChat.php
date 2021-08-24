<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupChat extends Base
{
    /**
     * 企业微信-外部联系人管理-客户群
     * This model is mapped to the table iqyweixin_externalcontact_group_chat
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_chat';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['member_list'] = $this->changeToArray($data['member_list']);
        $data['create_time'] = $this->changeToValidDate($data['create_time']);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
