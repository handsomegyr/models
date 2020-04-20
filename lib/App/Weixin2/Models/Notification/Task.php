<?php

namespace App\Weixin2\Models\Notification;

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
        $query = array(
            'scheduled_push_time' => array('$lte' => \App\Common\Utils\Helper::getCurrentTime($now)),
            'push_status' => $push_status,
        );
        $sort  = array('_id' => 1);
        $list = $this->find($query, $sort, 0, 1);
        if (empty($list['datas'])) {
            return null;
        } else {
            $task = $this->findOne(array(
                '_id' => $list['datas'][0]['_id'],
                '__FOR_UPDATE__' => true
            ));
            return $task;
        }
        return $task;
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
