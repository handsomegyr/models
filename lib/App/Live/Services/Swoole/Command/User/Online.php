<?php
namespace App\Live\Services\Swoole\Command\User;

use App\Live\Services\Swoole\Command\Base;

/**
 * 上线命令
 */
class Online extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);  
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        $is_superman = isset($msg['is_superman']) ? $msg['is_superman'] : false;
        
        // 判定该用户是否可以登录
        $loginRet = $server->getStorer()->login($client_id, $room_id, $user_id, $is_superman);
        if (empty($loginRet['success'])) {
            $server->sendErrorMessage($client_id, $loginRet['code'], $loginRet['msg'], $loginRet['data']);
            return;
        }
        $userInfo = $loginRet['data']['userInfo'];
        $roomInfo = $loginRet['data']['roomInfo'];
        
        // 是否为VIP
        $is_vip = $server->getStorer()->isVip($room_id, $user_id);
        $is_vip = empty($is_vip) ? false : true;
        
        // 检测是否是主播
        $modelRoom = new \App\Live\Models\Room();
        $is_auchor = $modelRoom->isAuchor($roomInfo, $userInfo);
        
        // 获取响应消息
        $userInfo = $this->outputUserInfo($userInfo);
        $userInfo['is_vip'] = $is_vip;
        $userInfo['is_auchor'] = $is_auchor;
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('online', $msg, $roomInfo, $userInfo);
        
        // 广播
        $server->broadcastJson($resMsg);
    }
}