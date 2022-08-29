<?php

namespace App\Common\Models\Qyweixin\Mysql\Auth;

use App\Common\Models\Base\Mysql\Base;

class AuthCorp extends Base
{
    /**
     * 企业微信-授权方企业信息
     * This model is mapped to the table iqyweixin_auth_corp
     */
    public function getSource()
    {
        return 'iqyweixin_auth_corp';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['access_token_expire'] = $this->changeToValidDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToValidDate($data['jsapi_ticket_expire']);
        $data['verified_end_time'] = $this->changeToValidDate($data['verified_end_time']);
        return $data;
    }
}
