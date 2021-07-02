<?php

namespace App\Qyweixin\Models\Notification;

class Task extends \App\Common\Models\Qyweixin\Notification\Task
{

    // 推送方式 1:发送应用消息 2:发送消息到群聊会话 3:发送互联企业消息 4:发送企业群发消息
    const NOTIFY_BY_AGENT_MESSAGE = 1;

    const NOTIFY_BY_APPCHAT = 2;

    const NOTIFY_BY_LINKEDCORP_MESSAGE = 3;

    const NOTIFY_BY_EXTERNALCONTACT_ADD_MSG_TEMPLATE = 4;

    /**
     * 根据推送状态获取并锁住一条任务
     *
     * @param int $now            
     */
    public function getAndLockOneTask4ByPushStatus($push_status, $now)
    {
        $query = array(
            'scheduled_push_time' => array(
                '$lte' => \App\Common\Utils\Helper::getCurrentTime($now),
                '$gte' => \App\Common\Utils\Helper::getCurrentTime(strtotime(date("Y-m-d", $now) . " 00:00:00"))
            ),
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
    }

    public function getAndLockOneTask4ById($id, $now)
    {
        $task = $this->findOne(array(
            '_id' => $id,
            '__FOR_UPDATE__' => true
        ));
        return $task;
    }

    public function updatePushState($id, $status, $now, $task_process_total = 0)
    {
        $updateData = array();
        $updateData['push_status'] = $status;
        $updateData['push_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (!empty($task_process_total)) {
            $updateData['task_process_total'] = $task_process_total;
        }
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
