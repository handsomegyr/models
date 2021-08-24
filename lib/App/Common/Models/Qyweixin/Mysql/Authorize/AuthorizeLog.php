<?php

namespace App\Common\Models\Qyweixin\Mysql\Authorize;

use App\Common\Models\Base\Mysql\Base;

class AuthorizeLog extends Base
{

    /**
     * 企业微信-授权事件接收日志
     * This model is mapped to the table iqyweixin_authorize_log
     */
    public function getSource()
    {
        return 'iqyweixin_authorize_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['request_time'] = $this->changeToValidDate($data['request_time']);
        $data['response_time'] = $this->changeToValidDate($data['response_time']);

        $data['is_aes'] = $this->changeToBoolean($data['is_aes']);
        return $data;
    }
}
