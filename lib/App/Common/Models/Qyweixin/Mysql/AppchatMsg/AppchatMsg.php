<?php

namespace App\Common\Models\Qyweixin\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class AppchatMsg extends Base
{
    /**
     * 企业微信-群聊会话消息
     * This model is mapped to the table iqyweixin_appchat_msg
     */
    public function getSource()
    {
        return 'iqyweixin_appchat_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['safe'] = $this->changeToBoolean($data['safe']);
        return $data;
    }
}
