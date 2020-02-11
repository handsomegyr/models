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
        $list = $this->getModel()
            ->where('notification_task_id', $notification_task_id)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item;
            }
        }
        return $ret;
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
