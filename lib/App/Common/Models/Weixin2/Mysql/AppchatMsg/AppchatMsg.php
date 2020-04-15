<?php

namespace App\Common\Models\Weixin2\Mysql\AppchatMsg;

use App\Common\Models\Base\Mysql\Base;

class AppchatMsg extends Base
{
    /**
     * 微信-群聊会话消息
     * This model is mapped to the table iweixin2_appchat_msg
     */
    public function getSource()
    {
        return 'iweixin2_appchat_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['safe'] = $this->changeToBoolean($data['safe']);
        return $data;
    }
}
