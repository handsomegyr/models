<?php

namespace App\Task\Models;

class Log extends \App\Common\Models\Task\Log
{

    /**
     * 默认排序方式
     *            
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        $sort['log_time'] = -1;
        return $sort;
    }

    /**
     * 默认查询条件
     *
     * @return array
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }

    public function Log($task, $is_success, array $request, array $result)
    {
        $data = array();
        $data['task'] = $task;
        $data['is_success'] = $is_success;
        $data['request'] = \App\Common\Utils\Helper::myJsonEncode($request);
        $data['result'] = \App\Common\Utils\Helper::myJsonEncode($result);
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime();
        return $this->insert($data);
    }
}
