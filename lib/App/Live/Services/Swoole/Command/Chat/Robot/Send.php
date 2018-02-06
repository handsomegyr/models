<?php
namespace App\Live\Services\Swoole\Command\Chat\Robot;

use App\Live\Services\Swoole\Command\Base;

/**
 * 机器人发送聊天信息
 */
class Send extends Base
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
        $userInfo = array(
            'user_id' => $user_id,
            'openid' => $msg['openid'],
            'nickname' => $msg['nickname'],
            'avatar' => $msg['avatar'],
            'score' => 0
        );
        $userInfo['is_vip'] = false;
        $userInfo['is_auchor'] = false;
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('chat', $msg, $roomInfo, $userInfo);
        
        // 关键词过滤方法
        $resMsg['data'] = $server->strTrieFilter($msg['data']);
        $resMsg['talk_by'] = 'auto';
        // 广播
        $server->broadcastJson($resMsg);
    }
}