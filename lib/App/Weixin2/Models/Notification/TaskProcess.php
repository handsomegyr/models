<?php

namespace App\Weixin2\Models\Notification;

class TaskProcess extends \App\Common\Models\Weixin2\Notification\TaskProcess
{
    // 1。推送状态
    // 未推送
    const UNPUSH = 0;

    // 推送中
    const PUSHING = 1;

    // 推送完成
    const PUSH_OVER = 2;

    // 推送成功
    const PUSH_SUCCESS = 3;

    // 推送失败
    const PUSH_FAIL = 4;

    public function getInfoByTaskId($notification_task_id)
    {
        $info = $this->getModel()
            ->where('notification_task_id', $notification_task_id)
            ->first();
        $info = $this->getReturnData($info);
        return $info;
    }

    /**
     * 根据推送状态获取并锁住一条任务
     *
     * @param number $now            
     */
    public function getAndLockOneTask4ByPushStatus($push_status, $now)
    {
        $task = $this->getModel()
            ->where('push_time', '<=', date("Y-m-d H:i:s", $now))
            ->where('push_status', $push_status)
            ->orderBy('push_time', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->first();
        $task = $this->getReturnData($task);
        return $task;
    }

    /**
     * 登录
     *
     * @param string $name            
     * @param number $notification_task_id            
     * @param number $task_process_total            
     * @param number $now            
     * @return array
     */
    public function logon($name, $notification_task_id, $task_process_total, $now)
    {
        $data = array();
        $data['name'] = $name;
        $data['notification_task_id'] = $notification_task_id;
        $data['push_status'] = self::UNPUSH;
        $data['push_time'] = date("Y-m-d H:i:s", $now);
        $data['task_process_total'] = $task_process_total;
        $data['processed_num'] = 0;
        $data['success_num'] = 0;
        return $this->insert($data);
    }

    public function updatePushState($id, $status, $now)
    {
        $updateData = array();
        $updateData['push_status'] = $status;
        $updateData['push_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }

    public function updateTaskProcessTotal($id, $task_process_total)
    {
        $updateData = array();
        $updateData['task_process_total'] = $task_process_total;
        return $this->updateById($id, $updateData);
    }

    public function incProcessedNum($id, $processed_num, $is_success = false)
    {
        $updateData = array();
        $processed_num = abs($processed_num);
        $updateData['processed_num'] = DB::raw("processed_num+{$processed_num}");
        // 如果成功的话
        if ($is_success) {
            $updateData['success_num'] = DB::raw("success_num+{$processed_num}");
        }

        $affectRows = $this->updateById($id, $updateData);
        return $affectRows;
    }

    public function incSuccessNum($id, $success_num)
    {
        $updateData = array();
        $updateData['success_num'] = DB::raw("success_num+{$success_num}");

        $affectRows = $this->updateById($id, $updateData);
        return $affectRows;
    }
}
