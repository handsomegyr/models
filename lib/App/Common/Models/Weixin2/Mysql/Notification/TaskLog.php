<?php

namespace App\Common\Models\Weixin2\Mysql\Notification;

use App\Common\Models\Base\Mysql\Base;

class TaskLog extends Base
{
    /**
     * 微信-消息推送-推送任务日志
     * This model is mapped to the table iweixin2_notification_task_log
     */
    public function getSource()
    {
        return 'iweixin2_notification_task_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['openids'] = $this->changeToArray($data['openids']);
        $data['errors'] = $this->changeToArray($data['errors']);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        $data['push_time'] = $this->changeToValidDate($data['push_time']);
        $data['is_ok'] = $this->changeToBoolean($data['is_ok']);
        return $data;
    }
}
