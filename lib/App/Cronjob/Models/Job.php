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
        $now = \App\Common\Utils\Helper::getCurrentTime();
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
    public function startJob($_id, $nowTime = 0)
    {
        if (empty($nowTime)) {
            $last_execute_time = \App\Common\Utils\Helper::getCurrentTime(floor(time() / 60) * 60);
        } else {
            $last_execute_time = \App\Common\Utils\Helper::getCurrentTime($nowTime);
        }
        return $this->update(array(
            '_id' => $_id
        ), array(
            '$set' => array(
                'last_execute_time' => $last_execute_time
            )
        ));
    }

    /**
     * 获取记录时间
     *
     * @param string $_id            
     * @param string $result            
     */
    public function recordResult($_id, $result, $scriptExecuteTime)
    {
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
