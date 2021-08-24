<?php

namespace App\Common\Models\Qyweixin\Mysql\Notification;

use App\Common\Models\Base\Mysql\Base;

class TaskProcess extends Base
{
    /**
     * 企业微信-消息推送-推送任务处理
     * This model is mapped to the table iqyweixin_notification_task_process
     */
    public function getSource()
    {
        return 'iqyweixin_notification_task_process';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['push_time'] = $this->changeToValidDate($data['push_time']);
        return $data;
    }
}
