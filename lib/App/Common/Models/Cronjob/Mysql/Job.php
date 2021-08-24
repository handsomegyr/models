<?php

namespace App\Common\Models\Cronjob\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Job extends Base
{

    /**
     * 计划任务-任务管理
     * This model is mapped to the table icronjob_job
     */
    public function getSource()
    {
        return 'icronjob_job';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['last_execute_time'] = $this->changeToValidDate($data['last_execute_time']);
        return $data;
    }
}
