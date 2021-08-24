<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupMsgResult extends Base
{
    /**
     * 企业微信-外部联系人管理-企业群发消息发送结果
     * This model is mapped to the table iqyweixin_externalcontact_group_msg_result
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_msg_result';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);        
        $data['send_time'] = $this->changeToValidDate($data['send_time']);
        return $data;
    }
}
