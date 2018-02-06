<?php
namespace App\Live\Services\Swoole\Command\Auchor;

use App\Live\Services\Swoole\Command\Base;

/**
 * 点赞
 */
class Like extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        // 查询用户信息
        $modelUser = new \App\Live\Models\User();
        $userInfo = $modelUser->getInfoFromRedis($user_id);
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
        
        // 是否为VIP
        $is_vip = $server->getStorer()->isVip($room_id, $user_id);
        $is_vip = empty($is_vip) ? false : true;
        
        // 检测是否是主播
        $is_auchor = $modelRoom->isAuchor($roomInfo, $userInfo);
        
        // 计算房间的虚拟点赞人数
        $roomInfo['like_num_virtual'] = $modelRoom->calcVirtualLikeNum($roomInfo);
        $roomInfo['view_num_virtual'] = $modelRoom->getVirtualViewNum($room_id);
        
        // 获取响应消息
        $userInfo = $this->outputUserInfo($userInfo);
        $userInfo['is_vip'] = $is_vip;
        $userInfo['is_auchor'] = $is_auchor;
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('like', $msg, $roomInfo, $userInfo);
        
        // 广播
        $server->broadcastJson($resMsg);
    }
}