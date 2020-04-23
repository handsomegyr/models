<?php

namespace App\Common\Models\Weixin2\Mysql\Agent;

use App\Common\Models\Base\Mysql\Base;

class Agent extends Base
{
    /**
     * 微信-代理应用设置
     * This model is mapped to the table iweixin2_agent
     */
    public function getSource()
    {
        return 'iweixin2_agent';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);

        return $data;
    }
}
