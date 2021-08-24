<?php

namespace App\Common\Models\Qyweixin\Mysql\Notification;

use App\Common\Models\Base\Mysql\Base;

class Task extends Base
{
    /**
     * 企业微信-消息推送-推送任务
     * This model is mapped to the table iqyweixin_notification_task
     */
    public function getSource()
    {
        return 'iqyweixin_notification_task';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['scheduled_push_time'] = $this->changeToValidDate($data['scheduled_push_time']);
        $data['push_time'] = $this->changeToValidDate($data['push_time']);
        return $data;
    }

    public function getUploadPath()
    {
        return trim("qyweixin/notification/task", '/');
    }
}
