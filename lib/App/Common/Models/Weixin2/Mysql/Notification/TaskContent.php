<?php

namespace App\Common\Models\Weixin2\Mysql\Notification;

use App\Common\Models\Base\Mysql\Base;

class TaskContent extends Base
{
    /**
     * 微信-消息推送-推送任务内容
     * This model is mapped to the table iweixin2_notification_task_content
     */
    public function getSource()
    {
        return 'iweixin2_notification_task_content';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['openids'] = $this->changeToArray($data['openids']);
        $data['push_time'] = $this->changeToValidDate($data['push_time']);
        return $data;
    }
}
