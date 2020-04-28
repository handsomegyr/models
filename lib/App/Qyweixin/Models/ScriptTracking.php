<?php

namespace App\Qyweixin\Models;

class ScriptTracking extends \App\Common\Models\Qyweixin\ScriptTracking
{

    /**
     * 记录执行时间
     *
     * @param string $provider_appid            
     * @param string $authorizer_appid             
     * @param string $agentid           
     * @param string $type            
     * @param float $start_time            
     * @param float $end_time            
     * @param string $who            
     * @param string $sns_appid            
     */
    public function record($provider_appid, $authorizer_appid, $agentid, $type, $start_time, $end_time, $who, $sns_appid = "")
    {
        $datas = array(
            'provider_appid' => $provider_appid,
            'authorizer_appid' => $authorizer_appid,
            'agentid' => $agentid,
            'sns_appid' => $sns_appid,
            'who' => $who,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'execute_time' => abs($end_time - $start_time)
        );

        return $this->insert($datas);
    }
}
