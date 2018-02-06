<?php
namespace App\Live\Services\Swoole\Command\Barrage;

use App\Live\Services\Swoole\Command\Base;

/**
 * 获取历史弹幕记录
 */
class GetHistoryList extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        // 获取响应消息
        $resMsg = $this->getResponeMsg('getBarrageHistoryList', $msg);
        $resMsg['list'] = $server->getStorer()->getHistoryList('barrage', $room_id, 0, 5);
        
        $server->sendJson($resMsg);
    }
}