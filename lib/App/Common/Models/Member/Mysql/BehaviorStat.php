<?php

namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class BehaviorStat extends Base
{

    /**
     * 会员-会员行为统计管理
     * This model is mapped to the table imember_behavior_stat
     */
    public function getSource()
    {
        return 'imember_behavior_stat';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['stat_time'] = $this->changeToValidDate($data['stat_time']);
        return $data;
    }
}
