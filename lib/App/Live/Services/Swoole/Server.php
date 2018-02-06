<?php
namespace App\Live\Services\Swoole;

use Swoole;
use Swoole\Filter;
use Swoole\IFace\Protocol;

class Server extends Swoole\Protocol\CometServer
{

    protected $storer;

    /**
     *
     * @return \App\Live\Services\Swoole\Store\Redis
     */
    public function getStorer()
    {
        return $this->storer;
    }

    protected $_commands = null;

    protected $resTrie = null;

    public $_id = '';

    public $_users = array();

    public function __construct($config = array())
    {
        parent::__construct($config);
        
        // 跨域设置
        $this->origin = $config['server']['origin'];
        
        $this->_id = myMongoId(new \MongoId());
        $this->_users = array();
        
        // 初始化日志
        $this->initLogger();
        
        // 初始化存储服务
        $this->initStorer();
        
        // 初始化命令
        $this->initCommand();
    }

    /**
     * 接收到的所有消息解析，调用对应函数
     *
     * @see WSProtocol::onMessage()
     */
    public function onMessage($client_id, $ws)
    {
        // 接收消息解析json
        $msg = json_decode($ws['message'], true);
        if (empty($msg)) {
            $this->sendErrorMessage($client_id, 400001, "消息体格式不正确", $ws);
            return;
        }
        
        // 获取参数 cmd
        if (empty($msg['cmd'])) {
            $this->sendErrorMessage($client_id, 400002, "消息体中的cmd值为空", $msg);
            return;
        }
        
        $msg['server_id'] = $this->_id;
        $msg['client_id'] = $client_id;
        $msg['users'] = $this->_users;
        
        $cmd = $msg['cmd'];
        
        // 如果是polling的话,直播服务端向所有的客户端发送polling消息，使得客户端保持连接
        if ($cmd == 'polling') {
            $server = $this->getSwooleServer();
            foreach ($server->connections as $fd) {
                $msg['fd'] = $fd;
                $msg['client_id'] = $fd;
                $this->sendJson($msg);
            }
            return;
        }
        // 自动查找cmd 对应的 函数 cmd_开头的
        if (key_exists($cmd, $this->_commands)) {
            // 生成任务
            // print_r($msg);
            $this->getSwooleServer()->task(serialize($msg));
        } else {
            $this->sendErrorMessage($client_id, 400003, "未获得对应的 {$cmd} 方法", $msg);
        }
        return;
    }

    /**
     * 处理Task
     */
    public function onTask($serv, $task_id, $from_id, $data)
    {
        $req = unserialize($data);
        if ($req) {
            $command = $this->getCommand($req['cmd']);
            if (! empty($command)) {
                $command->execute($this, $req);
            }
        }
    }

    /**
     * 任务完成调用
     */
    public function onFinish($serv, $task_id, $data)
    {
        $this->send(substr($data, 0, 32), substr($data, 32));
    }

    /**
     * 下线时，通知所有人
     */
    public function onExit($client_id)
    {
        $msg = array();
        $msg['cmd'] = 'offline';
        $msg['client_id'] = $client_id;
        $msg['server_id'] = $this->_id;
        $msg['users'] = $this->_users;
        $this->getSwooleServer()->task(serialize($msg));
    }

    /**
     * 发送错误信息
     *
     * @param
     *            $client_id
     * @param
     *            $code
     * @param
     *            $msg
     */
    function sendErrorMessage($client_id, $code, $msg, $data = array())
    {
        $message = array(
            'cmd' => 'error',
            'code' => $code,
            'msg_type' => 'system_error',
            'msg' => $msg
        );
        if (! empty($data)) {
            $message['data'] = $data;
        }
        $message['client_id'] = $client_id;
        $this->sendJson($message);
    }

    /**
     * 发送JSON数据
     */
    public function sendJson($msg)
    {
        $client_id = $msg['client_id'];
        unset($msg['users']);
        unset($msg['client_id']);
        ksort($msg);
        $msg = json_encode($msg);
        if ($this->send($client_id, $msg) === false) {
            $this->onExit($client_id);
        }
    }

    /**
     * 广播JSON数据
     */
    public function broadcastJson(array $msg)
    {
        $client_id = $msg['client_id'];
        $room_id = $msg['room_id'];
        $broadcastUsers = $msg['users'];
        unset($msg['users']);
        unset($msg['client_id']);
        ksort($msg);
        $msg = json_encode($msg);
        $this->broadcast($client_id, $msg, $room_id, $broadcastUsers);
    }

    /**
     * 广播JSON数据 遍历房间内的其它人
     *
     * @param string $current_session_id            
     * @param string $msg            
     * @param string $room_id            
     */
    protected function broadcast($current_session_id, $msg, $room_id, array $broadcastUsers)
    {
        // $server = $this->getSwooleServer();
        // $list = $server->connections;
        $list = $this->storer->getRoomOnlineClients($room_id, $this->_id);
        foreach ($list as $client_id) {
            // 检查数据的有效性
            $userInfo = $this->storer->getUserInfoByClientId($client_id);
            if (empty($userInfo) || empty($userInfo['user_id']) || empty($userInfo['current_room_id'])) {
                continue;
            }
            if ($userInfo['current_room_id'] != $room_id) {
                continue;
            }
            // 如果指定了发送用户返回
            if (! empty($broadcastUsers)) {
                $user_id = $userInfo['user_id'];
                if (in_array($user_id, $broadcastUsers)) {
                    $this->send($client_id, $msg);
                }
                // 发送给管理员
                if ($client_id == $current_session_id) {
                    $this->send($client_id, $msg);
                }
            } else {
                $this->send($client_id, $msg);
            }
        }
    }

    /**
     * 脏词 过滤函数
     *
     * @param string $str            
     */
    function strTrieFilter($str)
    {
        if (empty($this->resTrie)) {
            // 加载脏词库
            // $this->resTrie = trie_filter_load($config['webim']['blackword_file']);
        }
        if (! empty($this->resTrie)) {
            $small_str = strtolower($str);
            $ret = trie_filter_search($this->resTrie, $small_str);
            if (! empty($ret)) {
                $arrRet = trie_filter_search_all($this->resTrie, $small_str);
                
                foreach ($arrRet as $k => $v) {
                    $str = substr_replace($str, str_repeat('*', $v[1]), $v[0], $v[1]);
                }
            }
        }
        return $str;
    }

    /**
     * 获取具体的命令
     *
     * @param string $cmd            
     * @return Base
     */
    protected function getCommand($cmd)
    {
        if (! key_exists($cmd, $this->_commands)) {
            return null;
        }
        return $this->_commands[$cmd];
    }

    protected function initLogger()
    {
        // 检测日志目录是否存在
        $log_file = $this->config['webim']['log_file'];
        $log_dir = dirname($log_file);
        if (! is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        if (! empty($log_file)) {
            $logger = new \Swoole\Log\FileLog($log_file);
        } else {
            $logger = new \Swoole\Log\EchoLog();
        }
        // Logger
        $this->setLogger($logger);
    }

    protected function initStorer()
    {
        // 用redis保存数据信息
        $this->storer = new \App\Live\Services\Swoole\Store\Redis($this->_id, $this->config);
    }

    protected function initCommand()
    {
        // 注册命令
        $this->_commands = array(
            // 获得版本号
            'getVersion' => new \App\Live\Services\Swoole\Command\GetVersion(),
            // -----------------------------------直播用户的行为---------------------------------------------
            // 登录
            'login' => new \App\Live\Services\Swoole\Command\User\Online(),
            // 下线
            'offline' => new \App\Live\Services\Swoole\Command\User\Offline(),
            // 获得当前在线
            'getOnlineList' => new \App\Live\Services\Swoole\Command\User\GetOnlineList(),
            // 发送信息
            'chat' => new \App\Live\Services\Swoole\Command\Chat\Send(),
            // 获得历史聊天记录
            'getChatHistoryList' => new \App\Live\Services\Swoole\Command\Chat\GetHistoryList(),
            // 发送弹幕
            'barrage' => new \App\Live\Services\Swoole\Command\Barrage\Send(),
            // 获得历史弹幕信息
            'getBarrageHistoryList' => new \App\Live\Services\Swoole\Command\Barrage\GetHistoryList(),
            // 点赞 给主播点赞
            'like' => new \App\Live\Services\Swoole\Command\Auchor\Like(),
            // -----------------------------------主播的行为---------------------------------------------
            // 设置VIP用户
            'setVip' => new \App\Live\Services\Swoole\Command\Vip\Set(),
            // 发送欢迎语
            'welcome' => new \App\Live\Services\Swoole\Command\Welcome\Send(),
            // 发送通知
            'notice' => new \App\Live\Services\Swoole\Command\Notice\Send(),
            // 播放直播
            'playLive' => new \App\Live\Services\Swoole\Command\Video\Play(),
            // 暂停直播
            'pauseLive' => new \App\Live\Services\Swoole\Command\Video\Pause(),
            // 继续直播
            'resumeLive' => new \App\Live\Services\Swoole\Command\Video\Resume(),
            // 关闭直播
            'closeLive' => new \App\Live\Services\Swoole\Command\Video\Close(),
            // -----------------------------------机器人的行为---------------------------------------------
            // 机器人发送聊天信息
            'robotChat' => new \App\Live\Services\Swoole\Command\Chat\Robot\Send(),
            // 机器人发送弹幕信息
            'robotBarrage' => new \App\Live\Services\Swoole\Command\Barrage\Robot\Send(),
            // 机器人登录
            'robotLogin' => new \App\Live\Services\Swoole\Command\User\Robot\Login(),
            // 机器人发送关闭直播消息
            'robotCloseLive' => new \App\Live\Services\Swoole\Command\Video\Robot\Close()
        );
    }

    public function __destruct()
    {
        echo "Destroying " . $this->_id . "\n";
        $server = $this->getSwooleServer();
        foreach ($server->connections as $fd) {
            $this->onExit($fd);
        }
    }
}

