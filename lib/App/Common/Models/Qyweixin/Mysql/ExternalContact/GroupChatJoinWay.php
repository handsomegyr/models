<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupChatJoinWay extends Base
{
    /**
     * 企业微信-外部联系人管理-配置客户群进群方式
     * This model is mapped to the table iqyweixin_externalcontact_group_chat_join_way
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_chat_join_way';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);        
        $data['auto_create_room'] = $this->changeToBoolean($data['auto_create_room']);
        $data['sync_time'] = $this->changeToValidDate($data['sync_time']);
        return $data;
    }
}
