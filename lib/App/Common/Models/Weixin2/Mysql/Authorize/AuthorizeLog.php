<?php

namespace App\Common\Models\Weixin2\Mysql\Authorize;

use App\Common\Models\Base\Mysql\Base;

class AuthorizeLog extends Base
{

    /**
     * 微信-授权事件接收日志
     * This model is mapped to the table iweixin2_authorize_log
     */
    public function getSource()
    {
        return 'iweixin2_authorize_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['request_time'] = $this->changeToMongoDate($data['request_time']);
        $data['response_time'] = $this->changeToMongoDate($data['response_time']);

        $data['is_aes'] = $this->changeToBoolean($data['is_aes']);
        return $data;
    }
}
