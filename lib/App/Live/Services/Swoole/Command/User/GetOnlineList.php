<?php
namespace App\Live\Services\Swoole\Command\User;

use App\Live\Services\Swoole\Command\Base;

/**
 * 获取在线列表
 */
class GetOnlineList extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        // 获取当前房间内的所有用户数
        $usersNumber = $server->getStorer()->getRoomOnlineClientNum($room_id);
        
        // 正式上线不应该将所有在线用户都传输出去
        $list = $server->getStorer()->getRoomOnlineClients($room_id);
        $online_list = array();
        foreach ($list as $fd) {
            $pieces = explode("_", $fd);
            if ($pieces[0] != $server->_id) {
                continue;
            }
            $fd = $pieces[1];
            $userInfo = $server->getStorer()->getUserInfoByClientId($fd);
            if (! empty($userInfo)) {
                $userInfo = $this->outputUserInfo($userInfo);
                // $userInfo['fd'] = $fd;
                $online_list[] = $userInfo;
            }
        }
        
        // 获取响应消息
        $resMsg = $this->getResponeMsg('getOnlineList', $msg);
        $resMsg['list'] = $online_list;
        $resMsg['number'] = $usersNumber;
        
        $server->sendJson($resMsg);
    }
}