<?php

namespace App\Common\Models\Qyweixin\Mysql\Agent;

use App\Common\Models\Base\Mysql\Base;

class Agent extends Base
{
    /**
     * 企业微信-代理应用设置
     * This model is mapped to the table iqyweixin_agent
     */
    public function getSource()
    {
        return 'iqyweixin_agent';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToMongoDate($data['jsapi_ticket_expire']);

        return $data;
    }
}
