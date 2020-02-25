<?php

namespace App\Common\Models\Points\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 积分-积分日志表管理
     * This model is mapped to the table ipoints_log
     */
    public function getSource()
    {
        return 'ipoints_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        $data['is_consumed'] = $this->changeToBoolean($data['is_consumed']);
        $data['consume_time'] = $this->changeToMongoDate($data['consume_time']);
        $data['is_sync'] = $this->changeToBoolean($data['is_sync']);
        $data['sync_time'] = $this->changeToMongoDate($data['sync_time']);
        return $data;
    }
}
