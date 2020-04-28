<?php

namespace App\Qyweixin\Models\Provider;

class ProviderLoginBindTracking extends \App\Common\Models\Qyweixin\Provider\ProviderLoginBindTracking
{

    /**
     * 记录执行时间
     *
     * @param string $Provider_appid            
     * @param string $type            
     * @param float $start_time            
     * @param float $end_time            
     * @param string $authorizer_appid            
     */
    public function record($Provider_appid, $type, $start_time, $end_time, $authorizer_appid)
    {
        $datas = array(
            'Provider_appid' => $Provider_appid,
            'authorizer_appid' => $authorizer_appid,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'execute_time' => abs($end_time - $start_time)
        );

        return $this->insert($datas);
    }
}
