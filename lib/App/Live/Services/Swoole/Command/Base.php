<?php
namespace App\Live\Services\Swoole\Command;

abstract class Base
{

    protected $version = 'v1.20180103.1';

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {}

    protected function outputUserInfo($userInfo)
    {
        // $fd = isset($userInfo['current_client_id']) ? $userInfo['current_client_id'] : '';
        $user_id = isset($userInfo['user_id']) ? $userInfo['user_id'] : '';
        $openid = isset($userInfo['openid']) ? $userInfo['openid'] : '';
        $nickname = isset($userInfo['nickname']) ? $userInfo['nickname'] : '';
        $headimgurl = isset($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '';
        $score = isset($userInfo['worth']) ? $userInfo['worth'] : 0;
        
        return array(
            'user_id' => $user_id,
            'openid' => $openid,
            'nickname' => $nickname,
            'avatar' => $headimgurl,
            'score' => $score
        );
    }

    protected function outputRoomInfo($roomInfo)
    {
        $room_id = isset($roomInfo['room_id']) ? $roomInfo['room_id'] : '';
        $name = isset($roomInfo['name']) ? $roomInfo['name'] : '';
        
        $view_num_virtual = isset($roomInfo['view_num_virtual']) ? $roomInfo['view_num_virtual'] : '0';
        $like_num_virtual = isset($roomInfo['like_num_virtual']) ? $roomInfo['like_num_virtual'] : '0';
        
        $view_num = isset($roomInfo['view_num']) ? $roomInfo['view_num'] : '0';
        $like_num = isset($roomInfo['like_num']) ? $roomInfo['like_num'] : '0';
        
        return array(
            'room_id' => $room_id,
            'name' => $name,
            'view_num_virtual' => $view_num_virtual,
            'like_num_virtual' => $like_num_virtual
        );
    }

    protected function getResponeMsg($cmd, $msg, $roomInfo = null, $userInfo = null)
    {
        $resMsg = array();
        $resMsg['client_id'] = $msg['client_id'];
        $resMsg['fd'] = $msg['client_id'];
        $resMsg['cmd'] = $cmd;
        
        $resMsg['from'] = $msg['from'];
        $resMsg['to'] = $msg['to'];
        
        // 房间信息
        $resMsg['room_id'] = $msg['room_id'];
        if (! empty($roomInfo)) {
            $resMsg['roomInfo'] = $roomInfo;
        }
        // 用户信息
        $resMsg['user_id'] = $msg['user_id'];
        if (! empty($userInfo)) {
            $resMsg['userInfo'] = $userInfo;
        }
        
        $resMsg['contentType'] = $msg['contentType'];
        $resMsg['source'] = $msg['source'];
        $resMsg['authtype'] = $msg['authtype'];
        $resMsg['channel'] = $msg['channel'];
        
        // 对所有人说的
        $resMsg['channal'] = 0;
        
        // 增加响应时间
        $resMsg['time'] = time();
        // 增加服务实例ID
        $resMsg['server_id'] = $msg['server_id'];
        // 增加版本号
        $resMsg['version'] = $this->version;
        
        // 增加版本号
        $resMsg['users'] = $msg['users'];
        
        return $resMsg;
    }

    protected function getRequestMsg($msg)
    {
        $msg['client_id'] = isset($msg['client_id']) ? $msg['client_id'] : 0;
        $msg['users'] = isset($msg['users']) ? $msg['users'] : array();
        $msg['from'] = isset($msg['from']) ? $msg['from'] : '';
        $msg['to'] = isset($msg['to']) ? $msg['to'] : '';
        $msg['room_id'] = isset($msg['room_id']) ? $msg['room_id'] : '';
        $msg['user_id'] = isset($msg['user_id']) ? $msg['user_id'] : '';
        $msg['source'] = isset($msg['source']) ? $msg['source'] : '';
        $msg['authtype'] = isset($msg['authtype']) ? $msg['authtype'] : '';
        $msg['channel'] = isset($msg['channel']) ? $msg['channel'] : 0;
        
        if (empty($msg['contentType'])) {
            $msg['contentType'] = 'text';
        }
        // print_r($msg);
        return $msg;
    }
}