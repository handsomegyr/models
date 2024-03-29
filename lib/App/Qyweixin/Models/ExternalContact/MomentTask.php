<?php

namespace App\Qyweixin\Models\ExternalContact;

class MomentTask extends \App\Common\Models\Qyweixin\ExternalContact\MomentTask
{
    /**
     * 根据任务id获取信息
     *
     * @param string $jobid            
     * @param string $authorizer_appid          
     */
    public function getInfoByJobId($jobid, $authorizer_appid)
    {
        $query = array();
        $query['jobid'] = $jobid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);
        return $info;
    }
}
