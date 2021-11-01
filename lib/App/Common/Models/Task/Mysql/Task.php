<?php

namespace App\Common\Models\Task\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Task extends Base
{

    /**
     * 任务-任务管理
     * This model is mapped to the table itask_task
     */
    public function getSource()
    {
        return 'itask_task';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_actived'] = $this->changeToBoolean($data['is_actived']);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['gifts'] = $this->changeToArray($data['gifts']);
        return $data;
    }
}
