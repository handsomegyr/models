<?php

namespace App\Common\Models\Cronjob\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Task extends Base
{

    /**
     * 计划任务-临时任务
     * This model is mapped to the table icronjob_task
     */
    public function getSource()
    {
        return 'icronjob_task';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['content'] = $this->changeToArray($data['content']);
        $data['is_done'] = $this->changeToBoolean($data['is_done']);
        $data['do_time'] = $this->changeToMongoDate($data['do_time']);
        return $data;
    }
}
