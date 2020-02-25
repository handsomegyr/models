<?php

namespace App\Points\Services;

class Api
{

    protected $modelPointRule = null;

    protected $modelPointUser = null;

    protected $modelPointLog = null;

    public function __construct()
    {
        $this->modelPointRule = new \App\Points\Models\Rule();
        $this->modelPointUser = new \App\Points\Models\User();
        $this->modelPointLog = new \App\Points\Models\Log();
    }

    /**
     * 获取或创建积分用户
     */
    public function getOrCreatePointUser($point_category, $user_id, $nickname, $headimgurl, $now, $current = 0, $freeze = 0, $consume = 0, $expire = 0, array $memo = array('memo' => ''))
    {
        if ('undefined' == $user_id) {
            return array();
        }
        // 根据积分分类和用户ID生成或获取ID
        $pointUserInfo = $this->modelPointUser->getInfoByUserId($user_id, $point_category);
        // 如果没有的话就生成
        if (empty($pointUserInfo)) {
            $pointUserInfo = $this->modelPointUser->create($point_category, $user_id, $nickname, $headimgurl, $now, $current, $freeze, $consume, $expire, $memo);
        } else {
            // 更新昵称和头像
            $pointUserInfo = $this->modelPointUser->updateUserInfo($pointUserInfo, $nickname, $headimgurl);
        }
        return $pointUserInfo;
    }


    /**
     * 添加或消耗积分
     *
     * @param string $point_category            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl               
     * @param string $uniqueId          
     * @param number $now          
     * @param number $points             
     * @param string $stage           
     * @param string $desc           
     * @param string $point_rule_id             
     * @param string $point_rule_code           
     * @param string $activity_id           
     * @param string $channel          
     * @param array $memo        
     */
    public function addOrReducePoint($point_category, $user_id, $user_name, $user_headimgurl, $uniqueId, $now, $points, $stage = "", $desc = "", $point_rule_id = "", $point_rule_code = "", $activity_id = "", $channel = "", array $memo = array('memo' => ''))
    {
        if ('undefined' == $user_id) {
            return false;
        }

        // 检查是否已经处理了
        $logInfo = $this->modelPointLog->getInfoByUniqueId($uniqueId, $point_rule_id, $point_category);
        if (!empty($logInfo)) {
            return false;
        }

        //获取或创建积分用户
        $pointUserInfo = $this->getOrCreatePointUser($point_category, $user_id, $user_name, $user_headimgurl, $now, 0,  0,  0, 0, $memo);
        if (empty($pointUserInfo)) {
            return false;
        }

        //添加或消耗积分
        $pointUserInfo = $this->modelPointUser->addOrReduce($pointUserInfo['id'], $points, $now);

        // 记录积分日志
        $this->modelPointLog->log($point_category, $user_id, $user_name, $user_headimgurl, $points, $now, $uniqueId, $point_rule_id, $point_rule_code, $activity_id, $channel, $stage, $desc, $memo);
        return $pointUserInfo;
    }
}
