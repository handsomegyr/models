<?php
namespace App\Live\Services\Swoole\Command\Chat;

use App\Live\Services\Swoole\Command\Base;

/**
 * 发送聊天
 */
class Send extends Base
{

    const MESSAGE_MAX_LEN = 1024;

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        // 获取请求消息
        $msg = $this->getRequestMsg($req);
        
        $client_id = isset($msg['client_id']) ? $msg['client_id'] : '';
        $from = isset($msg['from']) ? $msg['from'] : '';
        $room_id = isset($msg['room_id']) ? $msg['room_id'] : '';
        $user_id = isset($msg['user_id']) ? $msg['user_id'] : '';
        
        if (strlen($msg['data']) > self::MESSAGE_MAX_LEN) {
            $server->sendErrorMessage($client_id, 8009, '发送内容太长不能超过20个字');
            return;
        }
        
        // 是否在禁言中
        $isSilenced = $server->getStorer()->isProhibited($room_id, $user_id);
        if ($isSilenced) {
            $server->sendErrorMessage($client_id, 8011, '禁言中禁止发言');
            return;
        }
        
        // 查询用户信息
        $modelUser = new \App\Live\Models\User();
        $userInfo = $modelUser->getInfoFromRedis($user_id);
        
        // 获取房间信息
        $modelRoom = new \App\Live\Models\Room();
        $roomInfo = $modelRoom->getInfoFromRedis($room_id);
        
        // 如果该用户不是主播的话
        if (empty($userInfo['is_auchor'])) {
            // 发送频率默认为3秒 否则取房间设置里的值
            // if (isset($roomInfo['item_settings'][$source]['item5']['rate'])) {
            // $rate = intval($roomInfo['item_settings'][$source]['item5']['rate']);
            // } else {
            // $rate = isset($roomInfo['item_settings']['item5']['rate']) ? intval($roomInfo['item_settings']['item5']['rate']) : 3;
            // }
            $rate = 3;
            $rate = max($rate, 3);
            // 3秒发送一次
            $isLimited = $server->getStorer()->isSendFrequencyLimited('chat', $room_id, $user_id, $rate);
            if ($isLimited) {
                $server->sendErrorMessage($client_id, 8010, $rate . '秒内只能发言一次 ');
                return;
            }
        }
        
        // 是否为VIP
        $is_vip = $server->getStorer()->isVip($room_id, $user_id);
        $is_vip = empty($is_vip) ? false : true;
        
        // 检测是否是主播
        $is_auchor = $modelRoom->isAuchor($roomInfo, $userInfo);
        
        // 获取响应消息
        $userInfo = $this->outputUserInfo($userInfo);
        $userInfo['is_vip'] = $is_vip;
        $userInfo['is_auchor'] = $is_auchor;
        $roomInfo = $this->outputRoomInfo($roomInfo);
        $resMsg = $this->getResponeMsg('chat', $msg, $roomInfo, $userInfo);
        
        // 关键词过滤方法
        $resMsg['data'] = $server->strTrieFilter($msg['data']);
        // 广播
        $server->broadcastJson($resMsg);
        
        // 加入历史
        $server->getStorer()->addHistory('chat', $room_id, $resMsg);
    }
}