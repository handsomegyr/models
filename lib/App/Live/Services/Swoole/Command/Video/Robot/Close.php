<?php
namespace App\Live\Services\Swoole\Command\Video\Robot;

use App\Live\Services\Swoole\Command\Base;

/**
 * 机器人关闭直播
 *
 * @author 郭永荣
 *        
 */
class Close extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
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
        
        // 获取响应消息
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('closeLive', $msg, $roomInfo, null);
        $resMsg['talk_by'] = 'auto';
        
        // 广播
        $server->broadcastJson($resMsg);
    }
}