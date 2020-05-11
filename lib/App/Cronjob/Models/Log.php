<?php

namespace App\Cronjob\Models;

class Log extends \App\Common\Models\Cronjob\Log
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

    public function getInfoByJobname($job_name)
    {
        $query = array(
            'job_name' => $job_name
        );
        return $this->findOne($query);
    }

    public function recordResult($job_name, $result, $scriptExecuteTime)
    {
        $info = $this->getInfoByJobname($job_name);
        if (empty($info)) {
            return $this->insert(array(
                'job_name' => $job_name,
                'execute_result' => $result,
                'script_execute_time' => $scriptExecuteTime
            ));
        } else {
            return $this->update(array(
                '_id' => $info['_id']
            ), array(
                '$set' => array(
                    'execute_result' => $result,
                    'script_execute_time' => $scriptExecuteTime
                )
            ));
        }
    }
}
