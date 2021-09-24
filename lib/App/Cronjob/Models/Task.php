<?php

namespace App\Cronjob\Models;

class Task extends \App\Common\Models\Cronjob\Task
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => -1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    public function log($type, array $content, array $memo = array('memo' => ''))
    {
        $data = array();
        $data['type'] = intval($type);
        $data['content'] = \App\Common\Utils\Helper::myJsonEncode($content);
        $data['is_done'] = false;
        $data['do_num'] = 0;
        $data['memo'] = $memo;
        return $this->insert($data);
    }

    /**
     * 完成任务
     */
    public function finishTask($task, $do_time, array $memo = array())
    {
        $query = array('_id' => $task['_id']);
        $updateArr = array(
            'is_done' => true,
            'do_time' => \App\Common\Utils\Helper::getCurrentTime($do_time),
        );
        if (!empty($memo)) {
            $task['memo'][] = $memo;
            $updateArr['memo'] = $task['memo'];
        }
        $updateData = array();
        $updateData['$set'] = $updateArr;
        $updateData['$inc'] = array(
            'do_num' => 1,
        );
        return $this->update($query, $updateData);
    }

    public function recordTaskInfo($task, $is_done, $do_time, $memo)
    {
        $query = array('_id' => $task['_id']);
        $updateArr = array(
            'do_time' => \App\Common\Utils\Helper::getCurrentTime($do_time),
        );
        if (!empty($is_done)) {
            $updateArr['is_done'] = true;
        }
        if (!empty($memo)) {
            $task['memo'][] = $memo;
            $updateArr['memo'] = $task['memo'];
        }
        $updateData = array();
        $updateData['$set'] = $updateArr;
        $updateData['$inc'] = array(
            'do_num' => 1,
        );
        return $this->update($query, $updateData);
    }
}
