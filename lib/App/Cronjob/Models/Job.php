<?php

namespace App\Cronjob\Models;

class Job extends \App\Common\Models\Cronjob\Job
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

    /**
     * 获取全部符合要求的计划任务
     */
    public function getAll()
    {
        $now = getCurrentTime();
        $nowTime = time();
        $cmds = $this->findAll(array(
            'start_time' => array(
                '$lte' => $now
            ),
            'end_time' => array(
                '$gte' => $now
            )
        ));

        return $cmds;
    }

    /**
     * 记录开启任务时间
     *
     * @param string $_id            
     */
    public function startJob($_id)
    {
        return $this->update(array(
            '_id' => $_id
        ), array(
            '$set' => array(
                'last_execute_time' => getCurrentTime(floor(time() / 60) * 60)
            )
        ));
    }

    /**
     * 获取记录时间
     *
     * @param string $_id            
     * @param string $result            
     */
    public function recordResult($_id, $result, $startTime)
    {
        $scriptExecuteTime = sprintf("%08.2f", microtime(true) - $startTime);

        $log = new Log();
        $log->insert(array(
            'job_name' => $_id,
            'execute_result' => $result,
            'script_execute_time' => $scriptExecuteTime
        ));

        return $this->update(array(
            '_id' => $_id
        ), array(
            '$set' => array(
                'last_execute_result' => $result,
                'script_execute_time' => $scriptExecuteTime
            )
        ));
    }
}
