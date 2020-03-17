<?php

namespace App\Search\Models;

class Log extends \App\Common\Models\Search\Log
{


    /**
     * 记录日志
     *           
     * @param string $content            
     * @param number $log_time          
     * @param string $member_id            
     * @param string $user_id           
     * @param string $channel               
     * @param array $memo          
     */
    public function log($content, $log_time, $member_id, $user_id, $channel = "", array $memo = array('memo' => ''))
    {
        $data = array();
        $data['content'] = trim($content);
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime($log_time);
        $data['member_id'] = trim($member_id);
        $data['user_id'] = $user_id;
        $data['channel'] = $channel;
        $data['memo'] = $memo;

        return $this->insert($data);
    }
}
