<?php

namespace App\Weixin2\Models\Notification;

use DB;

class Task extends \App\Common\Models\Weixin2\Notification\Task
{

    // 推送方式 1:模板消息 2:群发消息 3:客服消息
    const NOTIFY_BY_TEMPLATEMSG = 1;

    const NOTIFY_BY_MASSMSG = 2;

    const NOTIFY_BY_CUSTOMMSG = 3;

    /**
     * 根据推送状态获取并锁住一条任务
     *
     * @param number $now            
     */
    public function getAndLockOneTask4ByPushStatus($push_status, $now)
    {
        $task = $this->getModel()
            ->where('scheduled_push_time', '<=', date("Y-m-d H:i:s", $now))
            ->where('push_status', $push_status)
            ->orderBy('scheduled_push_time', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->first();
        $task = $this->getReturnData($task);
        return $task;
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

    public function incProcessNum($id, $process_num, $is_success = true)
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
