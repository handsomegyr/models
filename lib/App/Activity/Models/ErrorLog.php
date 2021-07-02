<?php

namespace App\Activity\Models;

class ErrorLog extends \App\Common\Models\Activity\ErrorLog
{

    /**
     * 记录
     *
     * @param string $activity_id        
     * @param \Exception $e    
     * @param int $happen_time            
     * @return array
     */
    public function log($activity_id, \Exception $e, $happen_time = 0)
    {
        $data = array();
        $data['activity_id'] = $activity_id;
        $data['error_code'] = intval($e->getCode());
        $data['error_message'] = $e->getMessage();
        $data['happen_time'] = \App\Common\Utils\Helper::getCurrentTime($happen_time);
        $result = $this->insert($data);
        return $result;
    }
}
