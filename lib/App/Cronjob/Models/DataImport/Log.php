<?php

namespace App\Cronjob\Models\DataImport;

class Log extends \App\Common\Models\Cronjob\DataImport\Log
{

    /**
     * 记录
     *
     * @param string $current_cronjob_id            
     * @param string $returnback_cronjob_id            
     * @param string $stage            
     * @param string $running_id               
     * @param int $now          
     * @param string $desc            
     * @return array
     */
    public function log($current_cronjob_id, $returnback_cronjob_id, $stage, $running_id, $now, $desc = '')
    {
        $data = array();
        $data['current_cronjob_id'] = trim($current_cronjob_id);
        $data['returnback_cronjob_id'] = trim($returnback_cronjob_id);
        $data['stage'] = $stage;
        $data['running_id'] = $running_id;
        $data['desc'] = $desc;
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime($now);

        $result = $this->insert($data);

        return $result;
    }
}
