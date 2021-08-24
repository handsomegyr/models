<?php

namespace App\Common\Models\Weixin2\Mysql\Kf;

use App\Common\Models\Base\Mysql\Base;

class MsgRecord extends Base
{
    /**
     * 微信-聊天记录
     * This model is mapped to the table iweixin2_msgrecord
     */
    public function getSource()
    {
        return 'iweixin2_msgrecord';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['msgrecord_time'] = $this->changeToValidDate($data['msgrecord_time']);
        return $data;
    }
}
