<?php
namespace App\Live\Services\Swoole\Command\Video;

use App\Live\Services\Swoole\Command\Base;

/**
 * 继续视频播放
 */
class Resume extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        $modelUser = new \App\Live\Models\User();
        $userInfo = $modelUser->getInfoFromRedis($from);
        if (empty($userInfo)) {
            $server->sendErrorMessage($client_id, '8023', '未获得用户信息');
            return;
        }
        
        // 获取房间信息
        $modelRoom = new \App\Live\Models\Room();
        $roomInfo = $modelRoom->getInfoFromRedis($room_id);
        if (empty($roomInfo)) {
            return;
        }
        // 主播没有配置好的话
        if (empty($roomInfo['auchor_id'])) {
            return;
        }
        
        // 判断是否主播
        $is_auchor = $modelRoom->isAuchor($roomInfo, $userInfo);
        if (empty($is_auchor)) {
            // 如果需要记录日志
            $server->sendErrorMessage($client_id, 8009, '不是主播不能关闭直播');
            return false;
        }
        
        // 获取响应消息
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('resumeLive', $msg, $roomInfo, null);
        
        // 广播
        $server->broadcastJson($resMsg);
    }
}