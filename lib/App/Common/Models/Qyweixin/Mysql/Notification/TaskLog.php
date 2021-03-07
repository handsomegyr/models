<?php

namespace App\Common\Models\Qyweixin\Mysql\Notification;

use App\Common\Models\Base\Mysql\Base;

class TaskLog extends Base
{
    /**
     * 企业微信-消息推送-推送任务日志
     * This model is mapped to the table iqyweixin_notification_task_log
     */
    public function getSource()
    {
        return 'iqyweixin_notification_task_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['userids'] = $this->changeToArray($data['userids']);
        $data['errors'] = $this->changeToArray($data['errors']);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        $data['push_time'] = $this->changeToMongoDate($data['push_time']);
        $data['is_ok'] = $this->changeToBoolean($data['is_ok']);
        return $data;
    }
}
