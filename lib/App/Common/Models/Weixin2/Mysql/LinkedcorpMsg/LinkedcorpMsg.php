<?php

namespace App\Common\Models\Weixin2\Mysql\LinkedcorpMsg;

use App\Common\Models\Base\Mysql\Base;

class LinkedcorpMsg extends Base
{
    /**
     * 微信-互联企业消息
     * This model is mapped to the table iweixin2_linkedcorp_msg
     */
    public function getSource()
    {
        return 'iweixin2_linkedcorp_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['content_item'] = $this->changeToArray($data['content_item']);
        $data['emphasis_first_item'] = $this->changeToBoolean($data['emphasis_first_item']);
        $data['toall'] = $this->changeToBoolean($data['toall']);
        $data['safe'] = $this->changeToBoolean($data['safe']);
        return $data;
    }
}
