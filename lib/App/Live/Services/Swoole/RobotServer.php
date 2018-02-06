<?php
namespace App\Live\Services\Swoole;

class RobotServer extends \Swoole\Network\Server
{

    private $config = null;

    /**
     * 机器人
     *
     * @var \App\Live\Services\Swoole\Chat\Robot
     */
    private $robot = null;

    private $last_robot_time = 0;

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取机器人
     *
     * @var \App\Live\Services\Swoole\Chat\Robot
     */
    public function getRobot()
    {
        $currentTime = time();
        
        // 离上次生成时刻过了 5分钟后，需要重新生成
        $is_need_recreate = (($currentTime - $this->last_robot_time) > 5 * 60);
        if ($is_need_recreate) {
            try {
                echo "recreate robot happened at " . date("Y-m-d H:i:s", $currentTime) . "\n";
                $this->robot = null;
            } catch (\Exception $e) {
                echo "recreate robot failed.error message:" . $e->getCode() . ":" . $e->getMessage() . "\n";
            }
        }
        
        if (! $this->robot) {
            $this->robot = new \App\Live\Services\Swoole\Chat\Robot($this);
            $this->last_robot_time = $currentTime;
        }
        return $this->robot;
    }

    public function onWorkerStart($serv, $worker_id)
    {
        parent::onWorkerStart($serv, $worker_id);
        
        if ($worker_id == 0) {
            $server = $this;
            // 机器人
            swoole_timer_tick(1000, function ($timer_id) use($server) {
                echo "tick-1000ms start:{$server->port}\n";
                $this->getRobot()->doSend();
                // swoole_timer_clear($timer_id);
            });
        }
    }
}