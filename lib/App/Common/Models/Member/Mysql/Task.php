<?php

namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Task extends Base
{

    /**
     * 会员-会员完成任务日志管理
     * This model is mapped to the table imember_task_log
     */
    public function getSource()
    {
        return 'imember_task_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['complete_time'] = $this->changeToValidDate($data['complete_time']);
        return $data;
    }
}
