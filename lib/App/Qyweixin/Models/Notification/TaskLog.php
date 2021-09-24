<?php

namespace App\Qyweixin\Models\Notification;

class TaskLog extends \App\Common\Models\Qyweixin\Notification\TaskLog
{
    public function log($provider_appid, $authorizer_appid, $notification_task_process_id, $notification_task_id, $notification_task_name, $notification_method, $externalcontact_msg_template_chat_type, $agent_msg_id, $appchat_msg_id, $externalcontact_msg_template_id, $linkedcorp_msg_id, $changemsginfo_callback, $notification_task_content_id, $notification_task_content_name, $userids, $userid, $push_status, $log_time)
    {
        $data = array();
        $data['provider_appid'] = $provider_appid;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['notification_task_process_id'] = $notification_task_process_id;
        $data['notification_task_id'] = $notification_task_id;
        $data['notification_task_name'] = $notification_task_name;
        $data['notification_method'] = $notification_method;
        $data['externalcontact_msg_template_chat_type'] = $externalcontact_msg_template_chat_type;
        $data['agent_msg_id'] = $agent_msg_id;
        $data['appchat_msg_id'] = $appchat_msg_id;
        $data['externalcontact_msg_template_id'] = $externalcontact_msg_template_id;
        $data['linkedcorp_msg_id'] = $linkedcorp_msg_id;
        $data['changemsginfo_callback'] = $changemsginfo_callback;
        $data['notification_task_content_id'] = $notification_task_content_id;
        $data['notification_task_content_name'] = $notification_task_content_name;
        $data['userids'] = $userids;
        $data['userid'] = $userid;
        $data['push_status'] = $push_status;
        $data['push_time'] = \App\Common\Utils\Helper::getCurrentTime($log_time);
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime($log_time);
        $data['is_ok'] = 0;
        $data['errors'] = '';
        $data['process_num'] = 0;
        return $this->insert($data);
    }

    public function getInfoByTaskId($notification_task_id)
    {
        $info = $this->findOne(array(
            'notification_task_id' => $notification_task_id
        ));
        return $info;
    }

    /**
     * 锁住记录
     *
     * @param int $id            
     */
    public function lockLog($id)
    {
        $rule = $this->findOne(array(
            '_id' => $id,
            '__FOR_UPDATE__' => true
        ));
        return $rule;
    }

    /**
     * 根据推送任务ID获取并锁住任务日志列表
     *
     * @param number $notification_task_id            
     */
    public function getAndLockListByTaskId($notification_task_id, $push_status)
    {
        $ret = $this->findAll(array(
            'notification_task_id' => $notification_task_id,
            'push_status' => $push_status,
            '__FOR_UPDATE__' => true
        ), array('_id' => 1));
        return $ret;
    }

    /**
     * 获取重试发送任务日志列表
     */
    public function getRetryList($retry_num, $push_status)
    {
        $ret = $this->findAll(array(
            'process_num' => array('$lte' => $retry_num),
            'push_status' => $push_status
        ), array('_id' => 1));
        return $ret;
    }

    public function updatePushState($id, $status, $now, $is_ok, array $error = array(), $process_num = 0)
    {
        $updateData = array();
        $updateData['push_status'] = $status;
        $updateData['push_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $updateData['is_ok'] = empty($is_ok) ? 0 : 1;
        if (empty($error)) {
            $updateData['errors'] = "";
        } else {
            $updateData['errors'] = \App\Common\Utils\Helper::myJsonEncode($error);
        }

        $incData = array();
        $incData['process_num'] = $process_num;
        return $this->update(array('_id' => $id), array('$set' => $updateData, '$inc' => $incData));
    }
}
