<?php
namespace App\Live\Services\Swoole\Store;

class Redis
{
    // redis key前缀
    protected $prefix = "live::";
    
    // 配置
    protected $config = array();

    /**
     * redis client
     *
     * @var \Predis\Client
     */
    protected $redis;
    
    // 服务ID
    protected $service_id;

    protected $history = array();

    public function __construct($id, $config = array())
    {
        $this->service_id = $id;
        $this->config = $config;
        $this->redis = \Phalcon\DI::getDefault()->get('redis');
    }

    /**
     * 错误返回
     */
    public function returnError($code, $msg, $data = array())
    {
        return array(
            'success' => false,
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
    }

    /**
     * 正确返回
     */
    public function returnTrue($data = array())
    {
        return array(
            'success' => true,
            'code' => 0,
            'msg' => 'OK',
            'data' => $data
        );
    }

    /**
     * 用户登录
     */
    public function login($client_id, $room_id, $user_id, $is_superman = false)
    {
        // 获取用户信息
        $modelUser = new \App\Live\Models\User();
        $userInfo = $modelUser->getInfoFromRedis($user_id);
        if (empty($userInfo)) {
            return $this->returnError("400101", "该用户不存在!!");
        }
        
        // 获取房间的信息
        $modelRoom = new \App\Live\Models\Room();
        $roomInfo = $modelRoom->getInfoFromRedis($room_id);
        if (empty($roomInfo)) {
            return $this->returnError("400102", "该直播间不存在!!");
        }
        
        // 检查是否存在房间主播
        if (empty($roomInfo['auchor_id'])) {
            return $this->returnError("400103", "亲主播还没准备好，请稍等一下!!");
        }
        
        // 检测是否是主播 是主播直接跳过判定
        $is_auchor = $modelRoom->isAuchor($roomInfo, $userInfo);
        // 如果该用户不是该房间的主播的话
        if (empty($is_auchor)) {
            // 如果不是超级man
            if (empty($is_superman)) {
                if (empty($roomInfo['state'])) {
                    return $this->returnError("400104", "亲直播结束了，下次早点来噢!!");
                }
                if (! empty($roomInfo['state']) && strtotime($roomInfo['live_start_time']) > time()) {
                    return $this->returnError("400105", "亲主播还没准备好，请稍等一下!!");
                }
                // 检查房间是否已满
                // 房间上线人数
                $currentUserNum = $this->getRoomOnlineClientNum($room_id);
                if (! empty($roomInfo['view_max_num']) && $currentUserNum > $roomInfo['view_max_num']) {
                    return $this->returnError("400106", "亲目前太过火爆，请稍等或稍后再试!!");
                }
            }
        }
        
        $userInfo['current_service_id'] = $this->service_id;
        $userInfo['current_room_id'] = $room_id;
        $userInfo['current_client_id'] = $client_id;
        
        // 根据用户ID记录用户登录信息
        $this->addUser($userInfo);
        
        // 记录所有进入用户到 所有链接记录表
        $this->addUserInfoByClientId($client_id, $userInfo);
        
        // 将websocket客户端添加到房间上
        $this->addRoomOnlineClient($room_id, $this->service_id, $client_id);
        
        // 获取当前的房间的在线人数
        $client_num = $this->getRoomOnlineClientNum($room_id);
        
        // 用户登陆房间处理
        $modelRoom = new \App\Live\Models\Room();
        $roomInfo = $modelRoom->doLogin($roomInfo, false, $client_num);
        
        $returnInfo = array();
        $returnInfo['userInfo'] = $userInfo;
        $returnInfo['roomInfo'] = $roomInfo;
        return $this->returnTrue($returnInfo);
    }

    /**
     * 退出登录
     *
     * @param string $client_id            
     * @param string $service_id            
     * @param string $room_id            
     * @param string $user_id            
     */
    public function logout($client_id, $service_id, $room_id, $user_id)
    {
        if (! empty($user_id)) {
            // 移除线上用户
            $this->removeUser($user_id);
        }
        
        if (! empty($client_id)) {
            // 将该websocket客户端对应的用户信息删除
            $this->removeUserInfoByClientId($client_id);
        }
        
        if (! empty($client_id) && ! empty($service_id) && ! empty($room_id)) {
            // 移除websocket客户端
            $this->removeRoomOnlineClient($room_id, $service_id, $client_id);
        }
    }
    
    // --------------------------------------------直播服务实例相关----------------------------------------------
    
    // --------------------------------------------直播房间相关----------------------------------------------
    /**
     * 将websocket客户端添加到房间上
     *
     * @param string $room_id            
     * @param string $service_id            
     * @param string $client_id            
     */
    public function addRoomOnlineClient($room_id, $service_id, $client_id)
    {
        $key = $this->getRoomOnlineKey($room_id);
        $this->redis->sAdd($key, $service_id . '_' . $client_id);
        $this->redis->expire($key, 3600 * 24 * 2);
        
        $key1 = $this->getRoomOnlineKey($room_id, $service_id);
        $this->redis->sAdd($key1, $client_id);
        $this->redis->expire($key1, 3600 * 24 * 2);
    }

    public function removeRoomOnlineClient($room_id, $service_id, $client_id)
    {
        $key = $this->getRoomOnlineKey($room_id);
        $this->redis->sRem($key, $this->service_id . '_' . $client_id);
        
        $key1 = $this->getRoomOnlineKey($room_id, $service_id);
        $this->redis->sRem($key1, $client_id);
    }

    /**
     * 获取当前房间的websocket客户端数量
     *
     * @param string $room_id            
     * @return number
     */
    public function getRoomOnlineClientNum($room_id, $service_id = '')
    {
        $key = $this->getRoomOnlineKey($room_id, $service_id);
        $num = $this->redis->sCard($key);
        if (empty($num)) {
            $num = 0;
        }
        return intval($num);
    }

    /**
     * 获取当前房间内所有在线 用户详细信息
     *
     * @param string $room_id            
     * @return array
     */
    public function getRoomOnlineClients($room_id, $service_id = '')
    {
        $key = $this->getRoomOnlineKey($room_id, $service_id);
        $sMembers = $this->redis->sMembers($key);
        if (! empty($sMembers) && is_array($sMembers)) {
            return $sMembers;
        }
        return array();
    }

    protected function getRoomOnlineKey($room_id, $service_id = '')
    {
        $key = $this->prefix . 'online::room::' . $room_id;
        if (! empty($service_id)) {
            $key = $key . "::" . $service_id;
        }
        return $key;
    }
    
    // --------------------------------------------直播用户相关----------------------------------------------
    /**
     * 记录上线的用户信息
     *
     * @param array $info            
     */
    public function addUser($info)
    {
        $user_id = $info['user_id'];
        // 根据用户ID获取之前的用户信息
        $oldUserInfo = $this->getUser($user_id);
        // 如果存在的话
        if (! empty($oldUserInfo)) {
            $old_client_id = $oldUserInfo['current_client_id'];
            $old_room_id = $oldUserInfo['current_room_id'];
            $old_service_id = $oldUserInfo['current_service_id'];
            // 进行logout处理 有问题的???
            $this->logout($old_client_id, $old_service_id, $old_room_id, $user_id);
        }
        // 添加
        $this->redis->hSet($this->getUserKey(), $user_id, json_encode($info));
    }

    public function removeUser($user_id)
    {
        $key = $this->getUserKey($user_id);
        $this->redis->del($key);
    }

    public function getUser($user_id)
    {
        $info = $this->redis->hGet($this->getUserKey(), $user_id);
        if (empty($info)) {
            return array();
        }
        $info = json_decode($info, true);
        if (empty($info)) {
            return array();
        }
        return $info;
    }

    protected function getUserKey()
    {
        return $this->prefix . 'online::userlist';
    }
    
    // --------------------------------------------直播客户端相关----------------------------------------------
    public function addUserInfoByClientId($client_id, $userInfo)
    {
        $key = $this->getUserClientKey($client_id);
        $this->redis->set($key, serialize($userInfo));
        $this->redis->expire($key, 3600 * 24 * 2);
    }

    public function removeUserInfoByClientId($client_id)
    {
        $key = $this->getUserClientKey($client_id);
        $this->redis->del($key);
    }

    /**
     * 根据socket客户端ID获取用户信息
     *
     * @param string $client_id            
     * @return array
     */
    public function getUserInfoByClientId($client_id)
    {
        $key = $this->getUserClientKey($client_id);
        $ret = $this->redis->get($key);
        if (empty($ret)) {
            return array();
        }
        $info = unserialize($ret);
        if (! isset($info['authtype'])) {
            $info['authtype'] = '';
        }
        if (! isset($info['source'])) {
            $info['source'] = '';
        }
        if (! isset($info['channel'])) {
            $info['channel'] = '';
        }
        return $info;
    }

    protected function getUserClientKey($client_id)
    {
        return $this->prefix . 'online::client::' . str_pad($client_id, 10, "0", STR_PAD_LEFT) . '::' . $this->service_id;
    }
    
    // --------------------------------------------播放视频相关----------------------------------------------
    
    // --------------------------------------------历史消息相关----------------------------------------------
    /**
     * 消息纪录加入历史日志里面 ， 超过$history_max_size条 删除
     *
     * @param string $item            
     * @param string $room_id            
     * @param array $log            
     * @param number $history_max_size            
     */
    public function addHistory($item, $room_id, array $log, $history_max_size = 30)
    {
        $this->history[$item][$room_id][] = $log;
        
        if (count($this->history[$item][$room_id]) > $history_max_size) {
            // 丢弃服务器上本房间的历史消息
            array_shift($this->history[$item][$room_id]);
        }
        // 记录每个房间内的所有聊天纪录
        $key = $this->getHistoryKey($item, $room_id);
        $this->redis->zAdd($key, time(), serialize($log));
    }

    /**
     * 获取历史纪录
     *
     * @param string $item            
     * @param string $room_id            
     * @param number $offset            
     * @param number $num            
     * @return array
     */
    public function getHistoryList($item, $room_id, $offset = 0, $num = 30)
    {
        $key = $this->getHistoryKey($item, $room_id);
        if ($this->redis->exists($key)) {
            $end_num = $this->redis->zCard($key);
            if ($end_num >= $num) {
                $begin_num = $end_num - $num;
            } else {
                $begin_num = 0;
            }
            $history = $this->redis->zRange($key, $begin_num, $end_num);
            if (! empty($history)) {
                foreach ($history as $k => $v) {
                    $history[$k] = unserialize($v);
                }
            }
        }
        if (empty($history)) {
            $history = array();
        }
        return $history;
    }

    protected function getHistoryKey($item, $room_id)
    {
        return $this->prefix . 'History::' . $item . '::' . $room_id . '::' . date('Ymd');
    }
    
    // --------------------------------------------发送消息频率限制相关----------------------------------------------
    /**
     * 发送信息是否小于rate秒
     *
     * @param string $room_id            
     * @param string $user_id            
     * @param number $rate            
     * @param string $item            
     */
    public function isSendFrequencyLimited($item, $room_id, $user_id, $rate)
    {
        $key = $this->getSendFrequencyLimitKey($item, $room_id, $user_id);
        $isLimited = $this->redis->exists($key);
        if (empty($isLimited)) {
            // 增加key
            $this->redis->set($key, time());
            // 设置 $rate秒 过期
            $this->redis->expire($key, $rate);
        }
        return $isLimited;
    }

    protected function getSendFrequencyLimitKey($item, $room_id, $user_id)
    {
        return $this->prefix . 'sendfrequencylimit::' . $item . '::' . $room_id . '::' . $user_id;
    }
    
    // --------------------------------------------禁言相关----------------------------------------------
    /**
     * 查询用户是否禁言
     */
    public function isProhibited($room_id, $user_id)
    {
        // 全房间禁言
        if ($this->redis->sIsMember($this->getProhibitKey(), $user_id)) {
            return true;
        }
        // 某房间禁言
        if ($this->redis->sIsMember($this->getProhibitKey($room_id), $user_id)) {
            return true;
        }
        return false;
    }

    protected function getProhibitKey($room_id = "")
    {
        if (empty($room_id)) {
            // 全房间
            return $this->prefix . 'prohibit::all';
        } else {
            return $this->prefix . 'prohibit::' . $room_id;
        }
    }
    
    // --------------------------------------------VIP相关----------------------------------------------
    /**
     * 是某个用户成为某房间的VIP用户
     *
     * @param string $room_id            
     * @param string $user_id            
     * @param boolean $is_vip            
     */
    public function setVip($room_id, $user_id, $is_vip = true)
    {
        $key = $this->getVipKey($room_id, $user_id);
        if ($is_vip) {
            $this->redis->set($key, time());
            $this->redis->expire($key, 3600 * 24 * 2);
        } else {
            $this->redis->del($key);
        }
    }

    /**
     * 是否是某房间的VIP用户
     *
     * @param string $room_id            
     * @param string $user_id            
     * @return boolean
     */
    public function isVip($room_id, $user_id)
    {
        $key = $this->getVipKey($room_id, $user_id);
        $info = $this->redis->exists($key);
        return $info;
    }

    protected function getVipKey($room_id, $user_id)
    {
        return $this->prefix . 'vip::' . $room_id . '::' . $user_id;
    }
    
    // --------------------------------------------欢迎语相关----------------------------------------------
    /**
     * 放置欢迎语请求
     */
    public function pushWelcomeMsgRequest($request)
    {
        $key = $this->getWelcomeMsgRequestKey();
        $this->redis->rpush($key, json_encode($request));
    }

    /**
     * 获取欢迎语请求
     */
    public function popWelcomeMsgRequest()
    {
        $key2 = $this->getWelcomeMsgRequestKey();
        $welcomeMsgRequest = $this->redis->lpop($key2);
        if (! empty($welcomeMsgRequest)) {
            $welcomeMsgRequest = json_decode($welcomeMsgRequest, true);
        }
        if (empty($welcomeMsgRequest)) {
            $welcomeMsgRequest = array();
        }
        return $welcomeMsgRequest;
    }

    protected function getWelcomeMsgRequestKey()
    {
        $key = $this->prefix . 'list4welcomemsgrequest';
        $key = $this->getKeyFromConfig($key);
        return $key;
    }
    
    // -----------------------------------------------------------------
    public function pushRoomId4CloseLive($room_id)
    {
        $key = $this->getRoomId4CloseLiveKey();
        $this->redis->rpush($key, $room_id);
    }

    /**
     * 获取最前的关闭直播的房间
     */
    public function getRoomId4CloseLive()
    {
        $key2 = $this->getRoomId4CloseLiveKey();
        $room_id = $this->redis->lpop($key2);
        return $room_id;
    }

    protected function getRoomId4CloseLiveKey()
    {
        $key = 'live::roomidlist4closelive';
        $key = $this->getKeyFromConfig($key);
        return $key;
    }

    public function getKeyFromConfig($key)
    {
        $config = $this->config;
        if (! empty($config['redis']['key'])) {
            $key .= ('::' . $config['redis']['key']);
        }
        return $key;
    }
}