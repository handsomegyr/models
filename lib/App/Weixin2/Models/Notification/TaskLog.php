<?php

namespace App\Weixin2\Models\Notification;

use DB;

class TaskLog extends \App\Common\Models\Weixin2\Notification\TaskLog
{

    // 1。推送状态
    // 未推送
    const UNPUSH = 0;

    // 推送中
    const PUSHING = 1;

    // 推送成功
    const PUSH_SUCCESS = 2;

    // 推送失败
    const PUSH_FAIL = 3;

    public function log($component_appid, $authorizer_appid, $notification_task_process_id, $notification_task_id, $notification_task_name, $notification_method, $mass_msg_send_method_id, $template_msg_id, $mass_msg_id, $custom_msg_id, $notification_task_content_id, $notification_task_content_name, $openids, $openid, $tag_id, $push_status, $log_time)
    {
        $data = array();
        $data['component_appid'] = $component_appid;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['notification_task_process_id'] = $notification_task_process_id;
        $data['notification_task_id'] = $notification_task_id;
        $data['notification_task_name'] = $notification_task_name;
        $data['notification_method'] = $notification_method;
        $data['mass_msg_send_method_id'] = $mass_msg_send_method_id;
        $data['template_msg_id'] = $template_msg_id;
        $data['mass_msg_id'] = $mass_msg_id;
        $data['custom_msg_id'] = $custom_msg_id;
        $data['notification_task_content_id'] = $notification_task_content_id;
        $data['notification_task_content_name'] = $notification_task_content_name;
        $data['openids'] = $openids;
        $data['openid'] = $openid;
        $data['tag_id'] = $tag_id;
        $data['push_status'] = $push_status;
        $data['push_time'] = $log_time;
        $data['log_time'] = $log_time;
        $data['is_ok'] = 0;
        $data['error'] = '';
        $data['process_num'] = 0;
        return $this->insert($data);
    }

    public function getInfoByTaskId($notification_task_id)
    {
        $info = $this->getModel()
            ->where('notification_task_id', $notification_task_id)
            ->first();
        $info = $this->getReturnData($info);
        return $info;
    }

    /**
     * 锁住记录
     *
     * @param int $id            
     */
    public function lockLog($id)
    {
        $rule = $this->getModel()
            ->where('id', $id)
            ->lockForUpdate()
            ->first();
        $rule = $this->getReturnData($rule);
        return $rule;
    }

    /**
     * 根据推送任务ID获取并锁住任务日志列表
     *
     * @param number $notification_task_id            
     */
    public function getAndLockListByTaskId($notification_task_id, $push_status)
    {
        $list = $this->getModel()
            ->where('notification_task_id', $notification_task_id)
            ->where('push_status', $push_status)
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

    /**
     * 获取重试发送任务日志列表
     */
    public function getRetryList($retry_num, $push_status)
    {
        $list = $this->getModel()
            ->where('process_num', '<=', $retry_num)
            ->where('push_status', $push_status)
            ->orderBy('id', 'asc')
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

    public function updatePushState($id, $status, $now, $is_ok, array $error = array(), $process_num = 0)
    {
        $updateData = array();
        $updateData['push_status'] = $status;
        $updateData['push_time'] = date("Y-m-d H:i:s", $now);
        $updateData['is_ok'] = empty($is_ok) ? 0 : 1;
        if (empty($error)) {
            $updateData['error'] = "";
        } else {
            $updateData['error'] = \json_encode($error);
        }

        if ($process_num != 0) {
            if ($process_num > 0) {
                $process_num = abs($process_num);
                $updateData['process_num'] = DB::raw("process_num+{$process_num}");
            } else {
                $process_num = abs($process_num);
                $updateData['process_num'] = DB::raw("process_num-{$process_num}");
            }
        }

        return $this->updateById($id, $updateData);
    }
}
