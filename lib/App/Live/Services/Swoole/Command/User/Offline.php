<?php
namespace App\Live\Services\Swoole\Command\User;

use App\Live\Services\Swoole\Command\Base;

/**
 * 下线处理
 */
class Offline extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        if (empty($user_id)) {
            $userInfo = $server->getStorer()->getUserInfoByClientId($client_id);
            if (empty($userInfo)) {
                return;
            }
            $user_id = isset($userInfo['user_id']) ? $userInfo['user_id'] : '';
        } else {
            $modelUser = new \App\Live\Models\User();
            $userInfo = $modelUser->getInfoFromRedis($user_id);
            if (empty($userInfo)) {
                return;
            }
        }
        $current_room_id = $userInfo['current_room_id'];
        
        if (empty($room_id)) {
            $room_id = $current_room_id;
        }
        
        if (! empty($room_id)) {
            $modelRoom = new \App\Live\Models\Room();
            $roomInfo = $modelRoom->getInfoFromRedis($room_id);
            if (empty($roomInfo)) {
                return;
            }
        }
        
        // 退出登录 , 移除房间内的 相关信息
        $server->getStorer()->logout($client_id, $server->_id, $room_id, $user_id);
        
        // 获取响应消息
        $userInfo = $this->outputUserInfo($userInfo);
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('offline', $msg, $roomInfo, $userInfo);
        // 广播
        $server->broadcastJson($resMsg);
    }
}