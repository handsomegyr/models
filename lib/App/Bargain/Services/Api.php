<?php

namespace App\Bargain\Services;

class Api
{

    private $modelBargain;

    private $modelLog;

    private $modelBlackUser;

    private $modelAlphaUser;

    private $_isInstance = false;

    public function __construct()
    {
        $this->modelBargain = new \App\Bargain\Models\Bargain();
        $this->modelLog = new \App\Bargain\Models\Log();
        $this->modelAlphaUser = new \App\Bargain\Models\AlphaUser();
        $this->modelBlackUser = new \App\Bargain\Models\BlackUser();
    }

    /**
     * 发起砍价物
     */
    public function launchBargain($activity_id, $user_id, $user_name, $user_headimgurl, $code, $name, $worth, $quantity, $bargain_from, $bargain_to, $worth_min, $bargain_max, $bargain_num_limit, $is_both_bargain, $start_time, $end_time, $bargain_period, $now, array $memo = array('memo' => ''), $launchagain = false)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );

        $lockKey = cacheKey(__CLASS__, __METHOD__, $user_id, $code, $activity_id);
        $objLock = new \iLock($lockKey);
        if ($objLock->lock()) {
            $ret['error_code'] = -99;
            $ret['error_msg'] = '处于锁定状态，请稍后尝试';
            return $ret;
        }

        // 查找最近的砍价对象
        $bargainInfo = $this->modelBargain->getLatestInfoByUserIdAndBargainCode($user_id, $code, $activity_id, $now);

        // 如果找到的话
        if (!empty($bargainInfo)) {
            $bargainInfo['is_new'] = false; // 原有的砍价的记录
            // 检查是否已经过了能砍价的时间段
            if (!empty($bargainInfo['bargain_period']) && strtotime($bargainInfo['launch_time']) <= ($now - $bargainInfo['bargain_period'] * 3600)) {
                // 如果是再发起的话,那么就生成新的砍价对象
                if (!empty($launchagain)) {
                    $bargainInfo = null;
                }
            }
        }

        if (empty($bargainInfo)) {
            // 生成砍价物对象
            $bargainInfo = $this->modelBargain->create($activity_id, $user_id, $user_name, $user_headimgurl, $code, $name, $worth, $quantity, $bargain_from, $bargain_to, $worth_min, $bargain_max, false, $bargain_num_limit, $is_both_bargain, $start_time, $end_time, $bargain_period, $now, strtotime('0001-01-01 00:00:00'), $memo);
            if (empty($bargainInfo)) {
                $ret['error_code'] = -2;
                $ret['error_msg'] = '砍价物生成失败';
                return $ret;
            }
            $bargainInfo['is_new'] = true; // 新生成的砍价的记录
        }
        $ret['result'] = $bargainInfo;
        return $ret;
    }

    /**
     * 砍价
     *
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $bargain_id             
     * @param string $client_ip            
     * @param int $now          
     * @param array $memo            
     * @param boolean $isSystemDo            
     */
    public function bargain($user_id, $user_name, $user_headimgurl, $bargain_id, $client_ip, $now, array $memo = array('memo' => ''), $isSystemDo = false)
    {
        if ($this->_isInstance) {
            return $this->error(-1, '每个实例只能执行一次砍价，如需反复砍价，请分别实例化\App\Bargain\Services\Api类');
        }
        $this->_isInstance = true;

        // 锁定防止高并发,必须用的，否则就会砍穿的危险
        $objLock = new \iLock($bargain_id);
        if ($objLock->lock()) {
            $ret['error_code'] = -99;
            $ret['error_msg'] = '处于锁定状态，请稍后尝试';
            return $ret;
        }

        try {
            // 获取砍价物
            $bargainInfo = $this->modelBargain->getInfoById($bargain_id);
            // 人为砍价处理
            $ret = $this->doBargain($user_id, $user_name, $user_headimgurl, $bargainInfo, $client_ip, $now, $memo, false);
            if (!empty($ret['error_code'])) {
                return $ret;
            }
        } catch (\Exception $e) {
            $ret = array(
                'error_code' => -9999,
                'error_msg' => $e->getMessage()
            );
        }
        return $ret;
    }

    public function checkBargain($bargainInfo, $now)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );

        // 根据砍价物的设置信息,检查数量等限制条件
        if (empty($bargainInfo)) {
            $ret['error_code'] = -1;
            $ret['error_msg'] = '砍价物不存在';
            return $ret;
        } else {
            if (empty($bargainInfo['quantity'])) {
                $ret['error_code'] = -2;
                $ret['error_msg'] = '砍价物库存不足';
                return $ret;
            }
            if (!empty($bargainInfo['is_closed'])) {
                $ret['error_code'] = -3;
                $ret['error_msg'] = '砍价物已下线';
                return $ret;
            }

            if (strtotime($bargainInfo['start_time']) > $now) {
                $ret['error_code'] = -4;
                $ret['error_msg'] = '砍价物未开始';
                return $ret;
            }

            if (strtotime($bargainInfo['end_time']) < $now) {
                $ret['error_code'] = -5;
                $ret['error_msg'] = '砍价物已结束';
                return $ret;
            }

            // 检查砍价次数限制
            if ($bargainInfo['bargain_num_limit'] > 0 && $bargainInfo['bargain_num_limit'] <= $bargainInfo['total_bargain_num']) {
                $ret['error_code'] = -6;
                $ret['error_msg'] = "该砍价明细被砍次数已超过限制,已无法再次砍价";
                return $ret;
            }

            // 如果砍价明细的最终价格为0或已经小于砍价物的最低价格的时候
            if (empty($bargainInfo['current_worth']) || ($bargainInfo['current_worth'] <= $bargainInfo['worth_min'])) {
                $ret['error_code'] = -7;
                $ret['error_msg'] = "已砍到最低价";
                return $ret;
            }
        }

        $ret['result'] = $bargainInfo;
        return $ret;
    }

    /**
     * 某用户进行砍价的处理
     *
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param array $bargainInfo          
     * @param array $client_ip              
     * @param int $now          
     * @param array $memo            
     * @param boolean $isSystemDo            
     */
    private function doBargain($user_id, $user_name, $user_headimgurl, array $bargainInfo, $client_ip, $now, array $memo = array('memo' => ''), $isSystemDo = false)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );

        // 检查砍价物信息
        $bargainCheckResult = $this->checkBargain($bargainInfo, $now);
        if (!empty($bargainCheckResult['error_code'])) {
            $ret['error_code'] = -1;
            $ret['error_msg'] = $bargainCheckResult['error_msg'];
            return $ret;
        }
        $bargainInfo = $bargainCheckResult['result'];

        $bargain_id = ($bargainInfo['_id']);
        // 如果是人为砍的话,进行检查是否已砍过
        if (!$isSystemDo) {
            $myLogInfo = $this->modelLog->getInfoByUserIdAndBargainId($user_id, $bargain_id);
            if (!empty($myLogInfo)) {
                $ret['error_code'] = -9;
                $ret['error_msg'] = "您已经砍过价了";
                return $ret;
            }
        }

        // 根据规则获取随机金额
        $bargainAmountNumInfo = $this->getBargainAmountAndNum($bargainInfo);
        $bargain_amount_generator = $bargainAmountNumInfo['bargain_amount_generator'];
        $bargain_num = $bargainAmountNumInfo['bargain_num'];

        // 更新砍价物的砍价信息
        $this->modelBargain->incBargain($bargainInfo, $bargain_amount_generator, $bargain_num, $now);
        // 重新获取
        $newBargainInfo = $this->modelBargain->getInfoById($bargainInfo['_id']);
        if (empty($newBargainInfo)) {
            $ret['error_code'] = -10;
            $ret['error_msg'] = "砍价物的砍价信息更新失败";
            return $ret;
        }

        // 是否已经砍到了最低价值
        $is_to_minworth = ($newBargainInfo['current_worth'] - $newBargainInfo['worth_min']) > 0 ? false : true;
        if ($is_to_minworth) {
            // 增加如果砍到了最低价值的时候，设置一个标志位
            $this->modelBargain->setBargainToMinworth($newBargainInfo, $now);
        }

        // 记录信息
        $memo['bargain_code'] = $bargainInfo['code'];
        $memo['bargain_name'] = $bargainInfo['name'];
        $memo['random'] = mt_rand(0, 10000);
        $logInfo = $this->modelLog->record($user_id, $user_name, $user_headimgurl, $client_ip, $bargain_id, $bargain_num, $bargain_amount_generator, $isSystemDo, $bargainInfo['activity_id'], $now, $memo);
        $ret['result'] = array(
            'logInfo' => $logInfo,
            'bargainInfo' => $bargainInfo,
            'is_bargain_to_minworth' => $is_to_minworth
        );

        return $ret;
    }

    private function result($msg = '', $result = '')
    {
        $rst = array(
            'success' => true,
            'message' => $msg,
            'result' => $result
        );
        return $rst;
    }

    private function error($code, $msg)
    {
        $rst = array(
            'success' => false,
            'error_code' => $code,
            'error_msg' => $msg
        );
        return $rst;
    }

    /**
     * 获取砍价金额
     */
    private function getBargainAmountAndNum($bargainInfo)
    {
        $ret = array();
        $ret['bargain_num'] = 1;
        // min(mt_rand(p1,p2))*@,极限,(当前价-底价))
        $remainWorth = ($bargainInfo['current_worth'] - $bargainInfo['worth_min']);
        if ($remainWorth <= 0) {
            $ret['bargain_amount_generator'] = 0;
        } else {
            // 检查发起人是否在黑名单中
            $blackGeneratorInfo = $this->modelBlackUser->getInfoByUserId($bargainInfo['user_id'], $bargainInfo['activity_id']);
            if (empty($blackGeneratorInfo)) {
                // 如果不在的话
                $bargainGeneratorInfo = $this->modelAlphaUser->getInfoByUserId($bargainInfo['user_id'], $bargainInfo['activity_id']);
                $alpha4Generator = empty($bargainGeneratorInfo) ? 1 : $bargainGeneratorInfo['alpha'];
            } else {
                // 是黑名单用户
                $alpha4Generator = empty($blackGeneratorInfo['alpha']) ? 0 : $blackGeneratorInfo['alpha'];
            }
            // 计算发起人的砍价金额
            $bargainGeneratorAmount = mt_rand($bargainInfo['bargain_from'], $bargainInfo['bargain_to']) * $alpha4Generator;
            $ret['bargain_amount_generator'] = min($bargainGeneratorAmount, $bargainInfo['bargain_max'], $remainWorth);
        }
        return $ret;
    }
}
