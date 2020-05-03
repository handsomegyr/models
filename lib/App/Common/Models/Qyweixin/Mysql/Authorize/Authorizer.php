<?php

namespace App\Common\Models\Qyweixin\Mysql\Authorize;

use App\Common\Models\Base\Mysql\Base;

class Authorizer extends Base
{
    /**
     * 企业微信-授权方应用设置
     * This model is mapped to the table iqyweixin_authorizer
     */
    public function getSource()
    {
        return 'iqyweixin_authorizer';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToMongoDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToMongoDate($data['jsapi_ticket_expire']);
        $data['wx_card_api_ticket_expire'] = $this->changeToMongoDate($data['wx_card_api_ticket_expire']);

        $data['suite_access_token_expire'] = $this->changeToMongoDate($data['suite_access_token_expire']);

        return $data;
    }
}
