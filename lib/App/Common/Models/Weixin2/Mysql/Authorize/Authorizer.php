<?php

namespace App\Common\Models\Weixin2\Mysql\Authorize;

use App\Common\Models\Base\Mysql\Base;

class Authorizer extends Base
{
    /**
     * 微信-授权方应用设置
     * This model is mapped to the table iweixin2_authorizer
     */
    public function getSource()
    {
        return 'iweixin2_authorizer';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToMongoDate($data['jsapi_ticket_expire']);
        $data['wx_card_api_ticket_expire'] = $this->changeToMongoDate($data['wx_card_api_ticket_expire']);

        return $data;
    }
}
