<?php

namespace App\Common\Models\Payment\Mysql;

use App\Common\Models\Base\Mysql\Base;

class NotifyLog extends Base
{

    /**
     * 支付-回调日志
     * This model is mapped to the table ipayment_notify_log
     */
    public function getSource()
    {
        return 'ipayment_notify_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
