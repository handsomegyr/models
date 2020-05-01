<?php

namespace App\Common\Models\Weixin2\Mysql\Msg;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{
    /**
     * 微信-消息与事件接收日志
     * This model is mapped to the table iweixin2_msg_log
     */
    public function getSource()
    {
        return 'iweixin2_msg_log';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['aes_info'] = $this->changeToArray($data['aes_info']);
        $data['request_params'] = $this->changeToArray($data['request_params']);

        $data['request_time'] = $this->changeToMongoDate($data['request_time']);
        $data['response_time'] = $this->changeToMongoDate($data['response_time']);

        $data['IsGiveByFriend'] = $this->changeToBoolean($data['IsGiveByFriend']);
        $data['IsRestoreMemberCard'] = $this->changeToBoolean($data['IsRestoreMemberCard']);
        $data['IsReturnBack'] = $this->changeToBoolean($data['IsReturnBack']);
        $data['IsChatRoom'] = $this->changeToBoolean($data['IsChatRoom']);
        $data['is_aes'] = $this->changeToBoolean($data['is_aes']);

        $data['ScanCodeInfo'] = $this->changeToBoolean($data['ScanCodeInfo']);
        $data['SendPicsInfo'] = $this->changeToBoolean($data['SendPicsInfo']);
        $data['SendLocationInfo'] = $this->changeToBoolean($data['SendLocationInfo']);
        $data['CopyrightCheckResult'] = $this->changeToBoolean($data['CopyrightCheckResult']);
        return $data;
    }
}
