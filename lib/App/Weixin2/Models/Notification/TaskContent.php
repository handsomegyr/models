<?php

namespace App\Weixin2\Models\Notification;

class TaskContent extends \App\Common\Models\Weixin2\Notification\TaskContent
{

    /**
     * 根据推送任务ID获取并锁住任务内容列表
     *
     * @param number $notification_task_id            
     */
    public function getAndLockListByTaskId($notification_task_id)
    {
        $ret = $this->findAll(array(
            'notification_task_id' => $notification_task_id,
            '__FOR_UPDATE__' => true
        ), array('_id' => 1));
        return $ret;
    }

    /**
     * 登录
     *
     * @param string $name            
     * @param string $notification_task_id            
     * @param array $openids              
     * @param string $tag_id            
     * @param int $now            
     * @return array
     */
    public function logon($name, $notification_task_id, array $openids, $tag_id, $now)
    {
        $data = array();
        $data['name'] = $name;
        $data['notification_task_id'] = $notification_task_id;
        $data['openids'] = \json_encode($openids);
        $data['tag_id'] = $tag_id;
        $data['push_status'] = 0;
        $data['push_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $data['task_process_total'] = 0;
        $data['processed_num'] = 0;
        $data['success_num'] = 0;
        return $this->insert($data);
    }

    public function updatePushState($id, $status, $now)
    {
        $updateData = array();
        $updateData['push_status'] = $status;
        $updateData['push_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function updateTaskProcessTotal($id, $task_process_total)
    {
        $updateData = array();
        $updateData['task_process_total'] = $task_process_total;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function incProcessedNum($id, $processed_num, $is_success = false)
    {
        $updateData = array();
        $processed_num = abs($processed_num);
        $updateData['processed_num'] = $processed_num;
        // 如果成功的话
        if ($is_success) {
            $updateData['success_num'] = $processed_num;
        }
        $affectRows = $this->update(array('_id' => $id), array('$inc' => $updateData));
        return $affectRows;
    }

    public function incSuccessNum($id, $success_num)
    {
        $updateData = array();
        $updateData['success_num'] = $success_num;
        $affectRows = $this->update(array('_id' => $id), array('$inc' => $updateData));
        return $affectRows;
    }
}
