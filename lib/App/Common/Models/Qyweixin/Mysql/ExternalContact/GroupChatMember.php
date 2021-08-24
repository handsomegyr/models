<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupChatMember extends Base
{
    /**
     * 企业微信-外部联系人管理-客户群成员列表
     * This model is mapped to the table iqyweixin_externalcontact_group_chat_member
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_chat_member';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['join_time'] = $this->changeToValidDate($data['join_time']);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
