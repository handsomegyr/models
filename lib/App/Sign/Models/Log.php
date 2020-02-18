<?php

namespace App\Sign\Models;

class Log extends \App\Common\Models\Sign\Log
{

    /**
     * 根据user_id获取信息
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return array
     */
    public function getInfoByUserId($user_id, $activity_id)
    {
        $query = array();
        $query['user_id'] = $user_id;
        $query['activity_id'] = $activity_id;
        $info = $this->findOne($query);

        return $info;
    }

    /**
     * 记录签到明细
     */
    public function log($activity_id, $user_id, $nickname, $headimgurl, $sign_time, $ip, $channel, array $memo = array('memo' => ''))
    {
        $data = array();
        $data['activity_id'] = $activity_id;
        $data['user_id'] = $user_id;
        $data['nickname'] = $nickname; // 用户昵称
        $data['headimgurl'] = $headimgurl; // 用户头像
        $data['sign_time'] = getCurrentTime($sign_time);
        $data['ip'] = $ip;
        $data['channel'] = $channel;
        $data['memo'] = $memo;
        $info = $this->insert($data);
        return $info;
    }
}
