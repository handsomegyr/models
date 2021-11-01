<?php

namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class BehaviorLog extends Base
{

    /**
     * 会员-会员行为日志管理
     * This model is mapped to the table imember_behavior_log
     */
    public function getSource()
    {
        return 'imember_behavior_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['act_time'] = $this->changeToValidDate($data['act_time']);
        return $data;
    }
}
