<?php
namespace App\Sign\Models;

class Log extends \App\Common\Models\Sign\Log
{

    /**
     * 记录签到明细
     */
    public function log($activity_id, $user_id, $nickname, $headimgurl, $sign_time, array $memo = array('memo'=>''))
    {
        $data = array();
        $data['activity_id'] = $activity_id;
        $data['user_id'] = $user_id;
        $data['nickname'] = $nickname; // 用户昵称
        $data['headimgurl'] = $headimgurl; // 用户头像
        $data['sign_time'] = $sign_time;
        $data['ip'] = getIp();
        $data['memo'] = $memo;
        $info = $this->insert($data);
        return $info;
    }
}