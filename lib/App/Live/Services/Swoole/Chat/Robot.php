<?php
namespace App\Live\Services\Swoole\Chat;

class Robot
{

    public $_id = '';

    /**
     * redis
     *
     * @var \Predis\Client
     */
    protected $redis;

    /**
     *
     * @var \App\Live\Services\Swoole\Chat\Server
     */
    private $server = null;

    private $isConnected = false;

    /**
     * 资源model
     *
     * @var \App\Live\Models\Resource
     */
    private $modelResource = null;

    /**
     * 房间model
     *
     * @var \App\Live\Models\Room
     */
    private $modelRoom = null;

    /**
     *
     * @return \App\Live\Services\Swoole\Store\Redis
     */
    private $storer = null;

    /**
     *
     * @return \App\Live\Services\Swoole\Store\Redis
     */
    public function getStorer()
    {
        return $this->storer;
    }

    function __construct(\App\Live\Services\Swoole\RobotServer $server)
    {
        $this->_id = myMongoId(new \MongoId());
        $this->server = $server;
        $objServer = ($this->server->protocol);
        $this->storer = $objServer->getStorer();
        $this->redis = \Phalcon\DI::getDefault()->get('redis');
        
        $this->modelResource = new \App\Live\Models\Resource();
        $this->modelRoom = new \App\Live\Models\Room();
    }

    public function doSend()
    {
        $client = $this->getWebSocketClient();
        if (empty($this->isConnected)) {
            if (! $client->connect(10)) {
                echo "connect to server failed.\n";
            } else {
                echo "connect to server success.\n";
                $this->isConnected = true;
            }
        }
        
        if ($this->isConnected) {
            // 发送polling命令 让server群发消息，这样保持连接，这个修改是解决阿里云slb 60s的问题
            $this->sendPolling($client);
            
            // 获取关闭直播房间ID
            $closeLiveRoomId = $this->getStorer()->getRoomId4CloseLive();
            if (! empty($closeLiveRoomId)) {
                $this->sendCloseLive($client, $closeLiveRoomId);
            }
            
            // 获取欢迎语
            $welcomeMsgRequest = $this->getStorer()->popWelcomeMsgRequest();
            if (! empty($welcomeMsgRequest)) {
                $this->sendWelcomeMsgChat($client, $welcomeMsgRequest['room_id'], $welcomeMsgRequest['num']);
            }
            
            // 获取机器人房间列表
            $roomIds = $this->modelRoom->getRoomIds4Robot();
            foreach ($roomIds as $room_id) {
                // 记录当前时间
                $current_time = time();
                
                // 获取房间的机器人配置信息
                $robotSettings = $this->modelRoom->getRobotSettings($room_id);
                if (empty($robotSettings)) {
                    continue;
                }
                
                // 获取上一次房间机器人操作信息
                $lastOperation = $this->getLastRoomRobotOperation($room_id);
                
                // 检查是否能够处理
                $isCanSend = $this->isCanDo($robotSettings, $lastOperation, $current_time);
                if (! $isCanSend) {
                    continue;
                }
                
                // 发送处理
                // 获取一个操作
                $operation = $this->getOperation($room_id, $robotSettings);
                
                // 记录本次的操作内容和时间
                $robotOperation = array(
                    'robot' => uniqid(),
                    'isCanSend' => $isCanSend,
                    'operation' => $operation,
                    'send_time' => $current_time,
                    'last_send_time' => empty($lastOperation['send_time']) ? 0 : $lastOperation['send_time']
                );
                $this->setLastRoomRobotOperation($room_id, $robotOperation);
                
                // 机器人
                $this->{$operation}($client, $room_id, $robotSettings);
            }
        }
    }

    /**
     *
     * @var \Swoole\Client\WebSocket
     */
    private $client = null;

    /**
     *
     * @var \Swoole\Client\WebSocket
     */
    protected function getWebSocketClient()
    {
        if (empty($this->client)) {
            $this->client = new \Swoole\Client\WebSocket('127.0.0.1', $this->server->port, '/');
        }
        return $this->client;
    }

    protected function isCanDo($robotSettings, $lastOperation, $current_time)
    {
        $isCanSend = true;
        // 检查是否到了发送的时间
        if (! empty($lastOperation)) {
            // 上次的发送时间
            $t0 = $lastOperation['send_time'];
            // 当前时间
            $t1 = $current_time;
            $period = $t1 - $t0;
            if (abs($period) < mt_rand(1, intval($robotSettings['rate']))) {
                $isCanSend = false;
            }
        }
        
        return $isCanSend;
    }

    /**
     * 聊天
     *
     * @param \Swoole\Client\WebSocket $client            
     */
    protected function sendChat($client, $room_id, $robotSettings)
    {
        // 是否使用公共默认机器人语言
        $is_use_default = $this->getIsUseDefault($robotSettings);
        $msg = array();
        $msg['cmd'] = 'robotChat';
        $msg['from'] = $this->_id;
        $msg['to'] = '';
        $msg['channel'] = 0;
        $msg['contentType'] = 'text';
        $msg['user_id'] = $this->_id;
        $msg['room_id'] = $room_id;
        $msg['openid'] = $this->getUniqueId();
        $msg['uniqueId'] = $this->getUniqueId();
        $msg['nickname'] = $this->modelResource->getRandom(1);
        $msg['avatar'] = $this->modelResource->getRandom(2);
        $msg['data'] = $this->getRandomMsg($room_id, $is_use_default);
        
        if (! empty($msg['data'])) {
            $client->send(json_encode($msg));
        }
    }

    /**
     * 弹幕
     *
     * @param \Swoole\Client\WebSocket $client            
     */
    protected function sendBarrage($client, $room_id, $robotSettings)
    {
        // 是否使用公共默认机器人语言
        $is_use_default = $this->getIsUseDefault($robotSettings);
        $msg = array();
        $msg['cmd'] = 'robotBarrage';
        $msg['from'] = $this->_id;
        $msg['to'] = '';
        $msg['channel'] = 0;
        $msg['contentType'] = 'text';
        $msg['user_id'] = $this->_id;
        $msg['room_id'] = $room_id;
        $msg['openid'] = $this->getUniqueId();
        $msg['uniqueId'] = $this->getUniqueId();
        $msg['nickname'] = $this->modelResource->getRandom(1);
        $msg['avatar'] = $this->modelResource->getRandom(2);
        $msg['data'] = $this->getRandomMsg($room_id);
        
        if (! empty($msg['data'])) {
            $client->send(json_encode($msg));
        }
    }

    protected function sendBarrageAndChat($client, $room_id, $robotSettings)
    {
        $this->sendBarrage($client, $room_id, $robotSettings);
        $this->sendChat($client, $room_id, $robotSettings);
    }

    /**
     * 来了
     *
     * @param \Swoole\Client\WebSocket $client            
     */
    protected function sendLogin($client, $room_id, $robotSettings)
    {
        $is_use_default = $this->getIsUseDefault($robotSettings);
        $msg = array();
        $msg['cmd'] = 'robotLogin';
        $msg['from'] = $this->_id;
        $msg['to'] = '';
        $msg['channel'] = 0;
        $msg['contentType'] = 'text';
        $msg['user_id'] = $this->_id;
        $msg['room_id'] = $room_id;
        $msg['openid'] = $this->getUniqueId();
        $msg['uniqueId'] = $this->getUniqueId();
        $msg['nickname'] = $this->modelResource->getRandom(1);
        $msg['avatar'] = $this->modelResource->getRandom(2);
        $client->send(json_encode($msg));
    }

    /**
     * 发送欢迎语
     *
     * @param \Swoole\Client\WebSocket $client            
     */
    protected function sendWelcomeMsgChat($client, $room_id, $num)
    {
        if (intval($num) <= 0) {
            return;
        }
        $num = intval($num);
        for ($i = 0; $i < $num; $i ++) {
            $msg = array();
            $msg['cmd'] = 'robotChat';
            $msg['from'] = $this->_id;
            $msg['to'] = '';
            $msg['channel'] = 0;
            $msg['contentType'] = 'text';
            $msg['user_id'] = $this->_id;
            $msg['room_id'] = $room_id;
            $msg['openid'] = $this->getUniqueId();
            $msg['uniqueId'] = $this->getUniqueId();
            $msg['nickname'] = $this->modelResource->getRandom(1);
            $msg['avatar'] = $this->modelResource->getRandom(2);
            $msg['data'] = $this->modelResource->getRandom(4);
            
            if (! empty($msg['data'])) {
                $client->send(json_encode($msg));
            }
        }
    }

    /**
     * 发送关闭直播消息
     *
     * @param \Swoole\Client\WebSocket $client            
     */
    protected function sendCloseLive($client, $room_id)
    {
        $msg = array();
        $msg['cmd'] = 'robotCloseLive';
        $msg['from'] = $this->_id;
        $msg['to'] = '';
        $msg['channel'] = 0;
        $msg['contentType'] = 'text';
        $msg['user_id'] = $this->_id;
        $msg['room_id'] = $room_id;
        $msg['openid'] = $this->getUniqueId();
        $msg['uniqueId'] = $this->getUniqueId();
        $client->send(json_encode($msg));
    }

    /**
     * 发送轮训消息
     *
     * @param \Swoole\Client\WebSocket $client            
     */
    protected function sendPolling($client)
    {
        $key2 = 'live::polling';
        $key2 = $this->getStorer()->getKeyFromConfig($key2);
        // 上次的发送时间
        $t0 = intval($this->redis->get($key2));
        // 当前时间
        $t1 = time();
        $period = $t1 - $t0;
        if ($period >= mt_rand(8, 15)) {
            $client->send(json_encode(array(
                'cmd' => 'polling'
            )));
            $this->redis->set($key2, $t1);
        }
    }

    protected function getRandomMsg($room_id, $is_use_default = true)
    {
        if (empty($is_use_default)) {
            $content1 = '';
        } else {
            // 获取通用机器人话语数量
            $content1 = $this->modelResource->getRandom(3);
            $key1Num = $this->modelResource->getCount(3);
        }
        
        // 获取自定义机器人话语数量
        // $key2 = $this->getRedisKey4RobotMsgList($room_id);
        // $content2 = $this->redis->sRandmember($key2);
        // $key2Num = $this->redis->sCard($key2);
        
        $key2 = '';
        $content2 = '';
        $key2Num = 0;
        
        if (empty($content1)) {
            return $content2;
        }
        if (empty($content2)) {
            return $content1;
        }
        
        // 根据以上的数量信息，决定
        $total = $key1Num + $key2Num;
        // 获取一个随机数
        $randomNum = mt_rand(0, $total);
        
        if ($randomNum >= 0 && $randomNum < $key1Num) {
            return $content1;
        } else {
            return $content2;
        }
    }

    protected function getUniqueId()
    {
        return (time() * 1000) . (substr(uniqid(), 0, 7));
    }

    /**
     * 获取上一次房间机器人操作信息
     *
     * @param string $room_id            
     */
    protected function getLastRoomRobotOperation($room_id)
    {
        $operation = $this->redis->hGet('live::lastRoomRobotOperation', $this->getRedisKey4LastRoomRobotOperation($room_id));
        if (empty($operation)) {
            return array();
        }
        $operation = json_decode($operation, true);
        if (empty($operation)) {
            return array();
        }
        return $operation;
    }

    /**
     * 设置房间机器人操作信息
     *
     * @param string $room_id            
     */
    protected function setLastRoomRobotOperation($room_id, $operation)
    {
        $this->redis->hSet('live::lastRoomRobotOperation', $this->getRedisKey4LastRoomRobotOperation($room_id), json_encode($operation));
    }

    protected function getRedisKey4LastRoomRobotOperation($room_id)
    {
        return 'live::lastRoomRobotOperation::' . $room_id . '_' . $this->_id;
    }
    
    // 根据某种算法决定机器人做某种操作
    protected function getOperation($room_id, $robotSettings)
    {
        // 是否使用公共默认机器人语言
        $is_use_default = $this->getIsUseDefault($robotSettings);
        
        // 获取通用机器人话语数量
        if (empty($is_use_default)) {
            $key1Num = 0;
        } else {
            $key1Num = $this->modelResource->getCount(3);
        }
        
        // 获取自定义机器人话语数量
        // $key2 = $this->getRedisKey4RobotMsgList($room_id);
        // $key2Num = $this->redis->sCard($key2);
        $key2 = '';
        $key2Num = 0;
        
        // 计算得到login的数量
        $loginNum = ($key1Num + $key2Num) * 2;
        // 根据以上的数量信息，决定
        $total = $key1Num + $key2Num + $loginNum;
        // 获取一个随机数
        $randomNum = mt_rand(0, $total);
        
        if ($randomNum >= 0 && $randomNum < ($key1Num + $key2Num)) {
            // 从操作中随机获取一个
            return $this->getOperation2($robotSettings);
        } else {
            // 是否开启来了的功能
            $isLogin = empty($robotSettings['operation']['login']) ? false : true;
            if ($isLogin) {
                return 'sendLogin';
            } else {
                return $this->getOperation2($robotSettings);
            }
        }
    }

    protected function getOperation2($robotSettings)
    {
        // 如果没有这一项的话
        if (empty($robotSettings['operation'])) {
            return 'sendChat';
        }
        if (! empty($robotSettings['operation']['chat']) && ! empty($robotSettings['operation']['barrage'])) {
            return 'sendBarrageAndChat';
        } elseif (! empty($robotSettings['operation']['barrage'])) {
            return 'sendBarrage';
        } else {
            return 'sendChat';
        }
    }

    /**
     * 是否使用公共默认机器人语言
     *
     * @param array $robotSettings            
     * @return boolean
     */
    protected function getIsUseDefault($robotSettings)
    {
        $is_use_default = ! isset($robotSettings['is_use_default']) ? true : (empty($robotSettings['is_use_default']) ? false : true);
        return $is_use_default;
    }
}