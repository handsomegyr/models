<?php

namespace App\Backend\Models;

class OperationLog extends \App\Common\Models\Backend\OperationLog
{

    /**
     * 记录
     *
     * @param $user_id 
     * @param $path 
     * @param $method 
     * @param $ip 
     * @param $params           
     * @return array
     */
    public function log($user_id, $path, $method, $ip, $params)
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['path'] = $path;
        $data['method'] = $method;
        $data['ip'] = $ip;
        $data['params'] = \\App\Common\Utils\Helper::myJsonEncode($params);
        $data['happen_time'] = \App\Common\Utils\Helper::getCurrentTime();
        $result = $this->insert($data);

        return $result;
    }
}
