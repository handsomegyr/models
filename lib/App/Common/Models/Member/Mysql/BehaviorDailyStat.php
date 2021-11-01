<?php

namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class BehaviorDailyStat extends Base
{

    /**
     * 会员-会员行为每日统计管理
     * This model is mapped to the table imember_behavior_daily_stat
     */
    public function getSource()
    {
        return 'imember_behavior_daily_stat';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['act_time'] = $this->changeToValidDate($data['act_time']);
        $data['stat_time'] = $this->changeToValidDate($data['stat_time']);
        return $data;
    }
}
