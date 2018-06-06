<?php
namespace App\Common\Models\Message\Mongodb;

use App\Common\Models\Base\Mongodb\Base;

class MsgCount extends Base
{

    /**
     * 消息-消息数量管理
     * This model is mapped to the table imessage_msg_count
     */
    public function getSource()
    {
        return 'imessage_msg_count';
    }
}