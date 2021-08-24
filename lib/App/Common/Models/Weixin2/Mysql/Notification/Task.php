<?php

namespace App\Common\Models\Weixin2\Mysql\Notification;

use App\Common\Models\Base\Mysql\Base;

class Task extends Base
{
    /**
     * 微信-消息推送-推送任务
     * This model is mapped to the table iweixin2_notification_task
     */
    public function getSource()
    {
        return 'iweixin2_notification_task';
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
        return trim("weixin2/notification/task", '/');
    }
}
