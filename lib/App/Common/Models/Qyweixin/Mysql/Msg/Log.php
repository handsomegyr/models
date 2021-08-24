<?php

namespace App\Common\Models\Qyweixin\Mysql\Msg;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{
    /**
     * 企业微信-消息与事件接收日志
     * This model is mapped to the table iqyweixin_msg_log
     */
    public function getSource()
    {
        return 'iqyweixin_msg_log';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['aes_info'] = $this->changeToArray($data['aes_info']);
        $data['request_params'] = $this->changeToArray($data['request_params']);

        $data['request_time'] = $this->changeToValidDate($data['request_time']);
        $data['response_time'] = $this->changeToValidDate($data['response_time']);

        $data['IsGiveByFriend'] = $this->changeToBoolean($data['IsGiveByFriend']);
        $data['IsRestoreMemberCard'] = $this->changeToBoolean($data['IsRestoreMemberCard']);
        $data['IsReturnBack'] = $this->changeToBoolean($data['IsReturnBack']);
        $data['IsChatRoom'] = $this->changeToBoolean($data['IsChatRoom']);
        $data['is_aes'] = $this->changeToBoolean($data['is_aes']);

        $data['ScanCodeInfo'] = $this->changeToArray($data['ScanCodeInfo']);
        $data['SendPicsInfo'] = $this->changeToArray($data['SendPicsInfo']);
        $data['SendLocationInfo'] = $this->changeToArray($data['SendLocationInfo']);
        $data['CopyrightCheckResult'] = $this->changeToArray($data['CopyrightCheckResult']);
        
        $data['contact_Department'] = $this->changeToArray($data['contact_Department']);        
        $data['contact_IsLeaderInDept'] = $this->changeToArray($data['contact_IsLeaderInDept']);

        $data['contact_ExtAttr'] = $this->changeToArray($data['contact_ExtAttr']);        
        $data['contact_AddUserItems'] = $this->changeToArray($data['contact_AddUserItems']);        
        $data['contact_DelUserItems'] = $this->changeToArray($data['contact_DelUserItems']);        
        $data['contact_AddPartyItems'] = $this->changeToArray($data['contact_AddPartyItems']);        
        $data['contact_DelPartyItems'] = $this->changeToArray($data['contact_DelPartyItems']);
        $data['BatchJob'] = $this->changeToArray($data['BatchJob']);
        $data['ApprovalInfo'] = $this->changeToArray($data['ApprovalInfo']);

        $data['Mode'] = $this->changeToBoolean($data['Mode']);

        return $data;
    }
}
