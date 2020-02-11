<?php

namespace App\Common\Models\Weixin2\Mysql\MassMsg;

use App\Common\Models\Base\Mysql\Base;

class SendMethod extends Base
{
    /**
     * 微信-群发消息发送方式
     * This model is mapped to the table iweixin2_mass_msg_send_method
     */
    public function getSource()
    {
        return 'iweixin2_mass_msg_send_method';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_to_all'] = $this->changeToBoolean($data['is_to_all']);
        $data['send_ignore_reprint'] = $this->changeToBoolean($data['send_ignore_reprint']);

        return $data;
    }
}
