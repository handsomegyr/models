<?php

namespace App\Common\Models\Payment\Mysql;

use App\Common\Models\Base\Mysql\Base;

class WeixinPayLog extends Base
{

    /**
     * 支付-微信支付回调日志
     * This model is mapped to the table ipayment_weixin_pay_log
     */
    public function getSource()
    {
        return 'ipayment_weixin_pay_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['notify_time'] = $this->changeToMongoDate($data['notify_time']);
        return $data;
    }
}
