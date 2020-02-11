<?php

namespace App\Weixin2\Models\Component;

class ComponentLoginBindTracking extends \App\Common\Models\Weixin2\Component\ComponentLoginBindTracking
{

    /**
     * 记录执行时间
     *
     * @param string $component_appid            
     * @param string $type            
     * @param float $start_time            
     * @param float $end_time            
     * @param string $authorizer_appid            
     */
    public function record($component_appid, $type, $start_time, $end_time, $authorizer_appid)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'execute_time' => abs($end_time - $start_time)
        );

        return $this->insert($datas);
    }
}
