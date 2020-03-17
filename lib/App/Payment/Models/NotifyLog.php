<?php

namespace App\Payment\Models;

class NotifyLog extends \App\Common\Models\Payment\NotifyLog
{
    public function log($channel, $message, $now, array $memo = array('memo' => ''))
    {
        $data = array();
        $data['channel'] = $channel;
        $data['message'] = $message;
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $data['memo'] = $memo;
        return $this->insert($data);
    }
}
