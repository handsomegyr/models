<?php

namespace App\Live\Models;

class Room extends \App\Common\Models\Live\Room
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => -1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    public function getFrontData($info)
    {
        // "share_settings": [ ],
        // "robot_settings": {
        // "is_open": false,
        // "login": 1,
        // "is_use_default": 1,
        // "rate": 10
        // },
        // "item_settings": [ ],
        // "behavior_settings": [ ],
        // "plugin_settings": [ ],
        // "view_settings": [ ],
        // "task_settings": [ ],
        // "emoji_settings": [ ],
        // "category_settings": [ ],
        // "coupon_settings": [ ],
        // "banner_settings": [ ],
        // "tag_settings": [ ],
        $data = array();
        $data['room_id'] = $info['_id'];
        $data['name'] = $info['name'];
        $data['start_time'] = date('Y-m-d H:i:s', strtotime($info['start_time']));
        $data['end_time'] = date('Y-m-d H:i:s', strtotime($info['end_time']));
        $data['is_opened'] = empty($info['is_opened']) ? 0 : 1;
        $data['headline'] = $info['headline'];
        $data['bg_pic'] = $this->getImagePath("/", $info['bg_pic']);
        $data['cover_pic'] = $this->getImagePath("/", $info['cover_pic']);
        $data['is_test'] = empty($info['is_test']) ? 0 : 1;
        $data['show_order'] = $info['show_order'];
        $data['is_direct'] = empty($info['is_direct']) ? 0 : 1;
        $data['state'] = $info['state'];

        $data['live_start_time'] = date('Y-m-d H:i:s', strtotime($info['live_start_time']));
        $data['live_end_time'] = date('Y-m-d H:i:s', strtotime($info['live_end_time']));
        $data['live_push_url'] = $info['live_push_url'];
        $data['live_play_url'] = $info['live_play_url'];
        $data['live_replay_url'] = $info['live_replay_url'];

        $data['live_paused_bg_pic'] = $this->getImagePath("/", $info['live_paused_bg_pic']);
        $data['live_closed_bg_pic'] = $this->getImagePath("/", $info['live_closed_bg_pic']);
        $data['live_closed_redirect_url'] = $info['live_closed_redirect_url'];

        $data['live_is_closed'] = empty($info['live_is_closed']) ? 0 : 1;
        $data['live_is_paused'] = empty($info['live_is_paused']) ? 0 : 1;
        $data['live_is_replay'] = empty($info['live_is_replay']) ? 0 : 1;

        $data['view_num'] = $info['view_num'];
        $data['like_num'] = $info['like_num'];
        return $data;
    }

    public function getRedisData($info)
    {
        // "share_settings": [ ],
        // "robot_settings": {
        // "is_open": false,
        // "login": 1,
        // "is_use_default": 1,
        // "rate": 10
        // },
        // "item_settings": [ ],
        // "behavior_settings": [ ],
        // "plugin_settings": [ ],
        // "view_settings": [ ],
        // "task_settings": [ ],
        // "emoji_settings": [ ],
        // "category_settings": [ ],
        // "coupon_settings": [ ],
        // "banner_settings": [ ],
        // "tag_settings": [ ],
        $data = array();
        $data['room_id'] = $info['_id'];
        $data['auchor_id'] = $info['auchor_id'];
        $data['name'] = $info['name'];
        $data['start_time'] = date('Y-m-d H:i:s', strtotime($info['start_time']));
        $data['end_time'] = date('Y-m-d H:i:s', strtotime($info['end_time']));
        $data['is_opened'] = empty($info['is_opened']) ? 0 : 1;
        $data['is_test'] = empty($info['is_test']) ? 0 : 1;
        $data['state'] = $info['state'];

        $data['live_start_time'] = date('Y-m-d H:i:s', strtotime($info['live_start_time']));
        $data['live_end_time'] = date('Y-m-d H:i:s', strtotime($info['live_end_time']));
        $data['live_is_closed'] = empty($info['live_is_closed']) ? 0 : 1;
        $data['live_is_paused'] = empty($info['live_is_paused']) ? 0 : 1;
        $data['live_is_replay'] = empty($info['live_is_replay']) ? 0 : 1;

        $data['view_max_num'] = $info['view_max_num'];
        $data['view_random_num'] = $info['view_random_num'];
        $data['view_base_num'] = $info['view_base_num'];
        $data['like_random_num'] = $info['like_random_num'];
        $data['like_base_num'] = $info['like_base_num'];

        // 机器人设置
        $data['robot_settings'] = $info['robot_settings'];

        return $data;
    }

    /**
     * 获取房间状态
     *
     * @param array $info            
     * @param boolean $is_virtual            
     * @return number
     */
    public function getState($info, $is_virtual = false)
    {
        $time = time();
        // 房间是否开启 是未开启
        if (empty($info['is_opened'])) {
            // 直播室关闭
            $info['state'] = 8;
        } else {
            // 是否是直播 永远是直播不是录播
            $info['is_live'] = true;
            if (empty($info['is_live'])) {
                // 录播页面
                $info['state'] = 7;
            } else {
                $live_start_time = strtotime($info['live_start_time']);

                if (!$is_virtual && $live_start_time > $time) {
                    // 直播未开始
                    $info['state'] = 1;
                } else {
                    // 直播是否结束
                    if (!empty($info['live_is_closed'])) {
                        // 直播是否回放
                        if (!empty($info['live_is_replay'])) {
                            // 直播是否有回放地址
                            if (!empty($info['live_replay_url'])) {
                                // 直播结束回放生成
                                $info['state'] = 6;
                            } else {
                                // 直播结束回放生成中
                                $info['state'] = 5;
                            }
                        } else {
                            // 直播结束无回放
                            $info['state'] = 4;
                        }
                    } else {
                        // 直播是否暂停
                        if (!empty($info['live_is_paused'])) {
                            // 直播暂停
                            $info['state'] = 3;
                        } else {
                            // 直播中
                            $info['state'] = 2;
                        }
                        // $info = $this->getRoomRedisDate($info);
                    }
                }
            }
        }
        return $info;
    }

    /**
     * 从redis中获取房间信息
     *
     * @param string $room_id            
     * @return array
     */
    public function getInfoFromRedis($room_id)
    {
        $info = $this->redis->hGet($this->getRedisKey(), $room_id);
        if (empty($info)) {
            return array();
        }
        $info = json_decode($info, true);
        if (empty($info)) {
            return array();
        }

        return $info;
    }

    /**
     * 记录信息到redis
     *
     * @param array $info            
     */
    public function saveInfoInRedis($info, $isForce = true)
    {
        // 记录用户信息到redis
        $info = $this->getRedisData($info);
        // 如果不存在
        if ($isForce || !$this->redis->hexists($this->getRedisKey(), $info['room_id'])) {
            $this->redis->hSet($this->getRedisKey(), $info['room_id'], \App\Common\Utils\Helper::myJsonEncode($info));
        }
        // 如果开启了机器人的话
        if (!empty($info['robot_settings']['is_open'])) {
            $this->addRoomId4Robot($info['room_id']);
        }
    }

    // 用户信息KEY
    protected function getRedisKey()
    {
        return $this->prefix . 'roominfolist';
    }

    public function doLogin($roomInfo, $is_robot = false, $client_num = 0)
    {
        $room_id = $roomInfo['room_id'];
        $auchor_id = $roomInfo['auchor_id'];

        // 计算房间的虚拟围观人数
        $roomInfo['view_num_virtual'] = $this->calcVirtualViewNum($roomInfo);

        // 计算围观峰值
        $roomInfo['view_peak_num'] = $this->calcViewPeakNum($room_id, $client_num);

        // 是真实的用户的时候
        if (empty($is_robot)) {
            // 增加房间的真实围观人数
            $roomInfo['view_num'] = $this->incrViewNum($room_id);
            // 获取房间的虚拟点赞人数
            $roomInfo['like_num_virtual'] = $this->getVirtualLikeNum($room_id, $auchor_id);
        } else {
            // 机器人的时候
            // 获取房间的真实围观人数
            $roomInfo['view_num'] = $this->getViewNum($room_id);
            // 计算房间的虚拟点赞人数
            $roomInfo['like_num_virtual'] = $this->calcVirtualLikeNum($roomInfo);
        }
        return $roomInfo;
    }

    /**
     * 增加房间的真实围观人数
     *
     * @param string $room_id            
     * @param int $num            
     * @return number
     */
    public function incrViewNum($room_id, $num = 1)
    {
        $key = $this->getViewNumKey4Redis($room_id);
        return intval($this->redis->incrBy($key, $num));
    }

    /**
     * 获取房间的真实围观人数
     *
     * @param string $room_id            
     * @return number
     */
    public function getViewNum($room_id)
    {
        $key = $this->getViewNumKey4Redis($room_id);
        return intval($this->redis->get($key));
    }

    protected function getViewNumKey4Redis($room_id)
    {
        return $this->prefix . 'view_num::' . $room_id;
    }

    /**
     * 计算房间的虚拟围观人数
     *
     * @param array $roomInfo            
     * @return number
     */
    public function calcVirtualViewNum($roomInfo)
    {
        // 增加房间的虚拟围观人数
        $room_id = $roomInfo['room_id'];
        $view_random_num = $this->getViewRandomNum($roomInfo['view_random_num']);
        return $roomInfo['view_base_num'] + $this->incrVirtualViewNum($room_id, $view_random_num);
    }

    /**
     * 增加房间的虚拟围观人数
     *
     * @param string $room_id            
     * @param int $num            
     * @return number
     */
    public function incrVirtualViewNum($room_id, $num = 1)
    {
        $key = $this->getVirtualViewNumKey4Redis($room_id);
        return intval($this->redis->incrBy($key, $num));
    }

    /**
     * 获取房间的虚拟围观人数
     *
     * @param string $room_id            
     * @return number
     */
    public function getVirtualViewNum($room_id)
    {
        $key = $this->getVirtualViewNumKey4Redis($room_id);
        return intval($this->redis->get($key));
    }

    /**
     * 前端显示登录人数KEY
     */
    protected function getVirtualViewNumKey4Redis($room_id)
    {
        return $this->prefix . 'view_num_virtual::' . $room_id;
    }

    protected function getViewRandomNum($view_random_num)
    {
        $view_random_num = ($view_random_num <= 0 ? intval($view_random_num) : 1);
        $view_random_num = max(1, $view_random_num);
        $view_random_num = mt_rand(1, $view_random_num);
        return $view_random_num;
    }

    /**
     * 计算房间的围观峰值
     *
     * @param string $room_id            
     * @param int $client_num            
     * @return number
     */
    public function calcViewPeakNum($room_id, $client_num)
    {
        // 设置围观峰值数据
        $this->setViewPeakNum($room_id, $client_num);
        // 围观峰值
        return $this->getViewPeakNum($room_id);
    }

    /**
     * 设置房间的围观峰值
     *
     * @param string $room_id            
     * @param int $client_num            
     * @return number
     */
    public function setViewPeakNum($room_id, $client_num)
    {
        $client_num = $client_num + 1;
        // 获取当前峰值
        $view_peak_num = $this->getViewPeakNum($room_id);
        // 如果当前的峰值小于websocket客户端数量的话
        if ($view_peak_num < $client_num) {
            $key = $this->getViewPeakNumKey4Redis($room_id);
            $this->redis->set($key, $client_num);
        }
    }

    /**
     * 获取房间的围观峰值
     *
     * @param string $room_id            
     * @return number
     */
    public function getViewPeakNum($room_id)
    {
        $key = $this->getViewPeakNumKey4Redis($room_id);
        return intval($this->redis->get($key));
    }

    protected function getViewPeakNumKey4Redis($room_id)
    {
        return $this->prefix . 'view_peak_num::' . $room_id;
    }

    /**
     * 计算房间的虚拟点赞人数
     *
     * @param array $roomInfo            
     * @return number
     */
    public function calcVirtualLikeNum($roomInfo)
    {
        $room_id = $roomInfo['room_id'];
        $auchor_id = $roomInfo['auchor_id'];
        $like_random_num = $this->getLikeRandomNum($roomInfo['like_random_num']);
        return $roomInfo['like_base_num'] + $this->incrVirtualLikeNum($room_id, $auchor_id, $like_random_num);
    }

    /**
     * 增加房间的虚拟点赞人数
     *
     * @param string $room_id            
     * @param string $auchor_id            
     * @param int $num            
     * @return number
     */
    public function incrVirtualLikeNum($room_id, $auchor_id, $num = 1)
    {
        $key = $this->getVirtualLikeNumKey4Redis($room_id, $auchor_id);
        return intval($this->redis->incrBy($key, $num));
    }

    /**
     * 获取房间的虚拟点赞人数
     *
     * @param string $room_id            
     * @param string $auchor_id            
     * @return number
     */
    public function getVirtualLikeNum($room_id, $auchor_id)
    {
        $key = $this->getVirtualLikeNumKey4Redis($room_id, $auchor_id);
        return intval($this->redis->get($key));
    }

    protected function getVirtualLikeNumKey4Redis($room_id, $auchor_id)
    {
        return $this->prefix . 'like_num_virtual::' . $room_id . '::' . $auchor_id;
    }

    protected function getLikeRandomNum($like_random_num)
    {
        $like_random_num = ($like_random_num <= 0 ? intval($like_random_num) : 1);
        $like_random_num = max(1, $like_random_num);
        $like_random_num = mt_rand(1, $like_random_num);
        return $like_random_num;
    }

    /**
     * 增加房间的真实点赞人数
     *
     * @param string $room_id            
     * @param string $auchor_id            
     * @param int $num            
     * @return number
     */
    public function incrLikeNum($room_id, $auchor_id, $num = 1)
    {
        $key = $this->getLikeNumKey4Redis($room_id, $auchor_id);
        return intval($this->redis->incrBy($key, $num));
    }

    /**
     * 获取房间的真实点赞人数
     *
     * @param string $room_id            
     * @param string $auchor_id            
     * @return number
     */
    public function getLikeNum($room_id, $auchor_id)
    {
        $key = $this->getVirtualLikeNumKey4Redis($room_id, $auchor_id);
        return intval($this->redis->get($key));
    }

    protected function getLikeNumKey4Redis($room_id, $auchor_id)
    {
        return $this->prefix . 'like_num::' . $room_id . '::' . $auchor_id;
    }

    /**
     * 添加开启机器人的房间ID
     */
    public function addRoomId4Robot($room_id)
    {
        $key2 = $this->getRoomIds4Robot4Redis();
        $this->redis->sadd($key2, $room_id);
    }

    /**
     * 获取开启机器人的房间ID列表
     */
    public function getRoomIds4Robot()
    {
        $key2 = $this->getRoomIds4Robot4Redis();
        $roomIds = $this->redis->smembers($key2);
        if (empty($roomIds)) {
            $roomIds = array();
        }
        return $roomIds;
    }

    protected function getRoomIds4Robot4Redis()
    {
        return $this->prefix . 'roomids4robot';
    }

    /**
     * 获取房间的机器人设置信息
     *
     * @param string $room_id            
     */
    public function getRobotSettings($room_id)
    {
        $roomInfo = $this->getInfoFromRedis($room_id);
        if (empty($roomInfo)) {
            return array();
        }

        if (empty($roomInfo['robot_settings']) || empty($roomInfo['robot_settings']['is_open'])) {
            return array();
        }

        // 获取房间状态
        $roomInfo = $this->getState($roomInfo);
        if (empty($roomInfo['state']) || !in_array($roomInfo['state'], array(
            2, // 2 直播中 or 直播暂停
            3
        ))) {
            return array();
        }

        return $roomInfo['robot_settings'];
    }

    public function getRedisKey4RobotMsgList($room_id)
    {
        return 'live::robotmsglist::' . $room_id;
    }

    public function isAuchor($roomInfo, $userInfo)
    {
        // 检测是否是主播 是主播直接跳过判定
        $is_auchor = false;
        if (!empty($userInfo['is_auchor'])) {
            // 如果该用户是该房间的主播的话
            if ($roomInfo['auchor_id'] == $userInfo['auchor_id']) {
                $is_auchor = true;
            }
        }
        return $is_auchor;
    }
}
