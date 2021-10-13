<?php

namespace App\Search\Services;

class Api
{

    protected $modelSearchKeyword = null;

    protected $modelSearchLog = null;

    public function __construct()
    {
        $this->modelSearchKeyword = new \App\Search\Models\Keyword();
        $this->modelSearchLog = new \App\Search\Models\Log();
    }

    /**
     * 记录
     *
     * @param string $content       
     * @param int $now             
     * @param int $member_id          
     * @param string $user_id           
     * @param string $channel           
     * @param array $memo        
     */
    public function log($content, $now, $member_id, $user_id, $channel = "", array $memo = array('memo' => ''))
    {
        if ('undefined' == $user_id) {
            return false;
        }

        // 记录日志
        $logInfo = $this->modelSearchLog->log($content, $now, $member_id, $user_id, $channel, $memo);

        //增加次数
        $this->modelSearchKeyword->incSearchNumByContent($content, 1, $now);

        return $logInfo;
    }
}
