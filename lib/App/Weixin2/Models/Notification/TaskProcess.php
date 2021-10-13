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

    // 推送关闭
    const PUSH_CLOSE = 5;

    public function getInfoByTaskId($notification_task_id)
    {
        $info = $this->findOne(array(
            'notification_task_id' => $notification_task_id
        ));
        return $info;
    }

    /**
     * 根据推送状态获取并锁住一条任务
     *
     * @param int $now            
     */
    public function getAndLockOneTask4ByPushStatus($push_status, $now)
    {
        $query = array(
            'push_time' => array(
                '$lte' => \App\Common\Utils\Helper::getCurrentTime($now),
                '$gte' => \App\Common\Utils\Helper::getCurrentTime(strtotime(date("Y-m-d", $now) . " 00:00:00"))
            ),
            'push_status' => $push_status,
        );
        $sort  = array('push_time' => 1, '_id' => 1);
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

    /**
     * 根据推送状态获取并锁住一条任务
     *
     * @param int $notification_task_id            
     */
    public function getAndLockOneTask4ByTaskid($notification_task_id, $now)
    {
        $task = $this->findOne(array(
            'notification_task_id' => $notification_task_id,
            '__FOR_UPDATE__' => true
        ));
        return $task;
    }

    /**
     * 登录
     *
     * @param string $name            
     * @param int $notification_task_id            
     * @param int $task_process_total            
     * @param int $now            
     * @return array
     */
    public function logon($name, $notification_task_id, $task_process_total, $now)
    {
        $data = array();
        $data['name'] = $name;
        $data['notification_task_id'] = $notification_task_id;
        $data['push_status'] = self::UNPUSH;
        $data['push_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $data['task_process_total'] = $task_process_total;
        $data['processed_num'] = 0;
        $data['success_num'] = 0;
        return $this->insert($data);
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
