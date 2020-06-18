<?php

namespace App\Exchange\Services;

class Api
{
    /**
     * @var \App\Prize\Models\Code
     */
    protected $modelCode = null;
    /**
     * @var \App\Prize\Models\Prize
     */
    protected $modelPrize = null;
    /**
     * @var \App\Exchange\Models\Exchange
     */
    protected $modelExchange = null;
    /**
     * @var \App\Exchange\Models\Limit
     */
    protected $modelLimit = null;
    /**
     * @var \App\Exchange\Models\Record
     */
    protected $modelRecord = null;
    /**
     * @var \App\Exchange\Models\Rule
     */
    protected $modelRule = null;

    protected $_isInstance = false;

    public function __construct()
    {
        $this->modelCode = new \App\Prize\Models\Code();
        $this->modelPrize = new \App\Prize\Models\Prize();
        $this->modelExchange = new \App\Exchange\Models\Exchange();
        $this->modelLimit = new \App\Exchange\Models\Limit();
        $this->modelRecord = new \App\Exchange\Models\Record();
        $this->modelRule = new \App\Exchange\Models\Rule();
    }

    /**
     * 做兑换动作
     *
     * @param string $activity_id            
     * @param string $identity_id            
     * @param number $now            
     * @param string $rule_id            
     * @param number $quantity            
     * @param number $score            
     * @param string $source            
     * @param array $user_info            
     * @param array $user_contact            
     * @param array $callbacks            
     * @param array $memo            
     * @return array
     */
    public function doExchange($activity_id, $identity_id, $now, $rule_id, $quantity = 1, $score = 0, $source = 'weixin', array $user_info = array(), array $identityContact = array(), array $callbacks = array(), array $memo = array('memo' => ''))
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        try {
            if ($this->_isInstance) {
                $ret['error_code'] = -100;
                $ret['error_msg'] = '每个抽奖实例只能执行一次doExchange方法，如需反复抽奖，请分别实例化Service_Api类';
                return $ret;
            }

            $this->_isInstance = true;

            // 控制活动流速，降低刷风险，同时只能有一个请求执行成功逻辑，将导致无法满足100%中奖要求
            $objLock = new \iLock(cacheKey($activity_id, $identity_id));
            if ($objLock->lock()) {
                $ret['error_code'] = -99;
                $ret['error_msg'] = '处于锁定状态，请稍后尝试';
                return $ret;
            }

            $score = intval($score);
            $quantity = intval($quantity);

            try {
                $this->modelRule->begin();

                // 获取规则信息
                $ruleInfo = $this->modelRule->lockRule($rule_id);
                if (empty($ruleInfo)) {
                    throw new \Exception("没有找到rule_id:{$rule_id}的兑换规则信息", -1);
                }

                // 检查时间
                if ($ruleInfo['allow_start_time']->sec >= $now) {
                    throw new \Exception("兑换未开始", -2);
                }

                if ($ruleInfo['allow_end_time']->sec < $now) {
                    throw new \Exception("兑换已结束", -3);
                }

                // 检查数量
                if ($ruleInfo['allow_number'] < $quantity) {
                    throw new \Exception("兑换奖品数量不足", -4);
                }

                // 需要积分
                if (!empty($ruleInfo['score'])) {
                    if ($score < $ruleInfo['score'] * $quantity) {
                        throw new \Exception("积分不足", -5);
                    }
                }

                // 检查是否是同一个活动
                if ($ruleInfo['activity_id'] != $activity_id) {
                    throw new \Exception("兑换规则不属于同一个活动", -6);
                }

                // 获取奖品信息
                $prize_id = $ruleInfo['prize_id'];
                $prizeInfo = $this->modelPrize->getInfoById($prize_id);
                if (empty($prizeInfo)) {
                    throw new \Exception("没有找到prize_id:{$prize_id}的奖品信息", -7);
                }

                // 检查兑换限制
                $limit = $this->checkLimit($activity_id, $identity_id, $now, $prize_id, $quantity);
                if ($limit == false) {
                    throw new \Exception('到达兑换限制的上限制', -8);
                }

                // 兑换奖品
                $exchangeRet = $this->modelRule->exchange($rule_id, $quantity);
                if (empty($exchangeRet)) {
                    throw new \Exception('竞争兑换奖品失败!', -9);
                }

                // 兑换花费积分
                $exchange_score = $ruleInfo['score'] * $quantity;

                $result = array();
                $result['identity_id'] = $identity_id;
                $result['prizeInfo'] = $prizeInfo;

                // 是否即时生效
                $isValid = !empty($prizeInfo['is_valid']) ? true : false;
                // 虚拟物品
                if (!empty($prizeInfo['is_virtual'])) {
                    // 发放虚拟奖品
                    if (!empty($prizeInfo['is_need_virtual_code'])) {
                        $code = $this->modelCode->getCode($prize_id, $now, $activity_id);
                        if (empty($code)) {
                            throw new \Exception('虚拟券不足!', -10);
                        }
                    }
                } else {
                    // 实物奖固定即时生效
                    $isValid = true;
                }

                // 虚拟奖品的券码
                $prizeCode = !empty($code) ? $code : array();

                // 记录兑换成功
                $exchangeInfo = $this->modelExchange->record($activity_id, $ruleInfo['prize_id'], $prizeInfo, $prizeCode, $identity_id, $user_info, $identityContact, $isValid, $source, $now, $quantity, $ruleInfo['score_category'], $exchange_score, $rule_id, $memo);

                if (!empty($exchangeInfo)) {
                    $exchangeInfo['exchange_id'] = $exchangeInfo['_id'];
                } else {
                    throw new \Exception('兑换成功信息记录失败', -11);
                }

                // 额外的处理
                if (!empty($callbacks)) {
                    foreach ($callbacks as $item) {
                        call_user_func_array($item, array(
                            $exchangeInfo
                        ));
                    }
                }
                $this->modelRule->commit();
                $this->modelRecord->record($activity_id, $identity_id, $source, 1, "恭喜您成功兑换了！", $rule_id, $exchangeInfo['_id']);
                $ret['result'] = $exchangeInfo;
            } catch (\Exception $e) {
                $this->modelRule->rollback();
                $this->modelRecord->record($activity_id, $identity_id, $source, $e->getCode(), $e->getMessage(), $rule_id, '');
                throw $e;
            }
        } catch (\Exception $e) {
            $ret['error_code'] = $e->getCode();
            $ret['error_msg'] = $e->getMessage();
        }
        return $ret;
    }

    /**
     * 检查指定操作指定规则的限制是否达到
     *
     * @param string $activity_id            
     * @param string $identity_id            
     * @param string $prize_id            
     */
    protected function checkLimit($activity_id, $identity_id, $now, $prize_id, $quantity)
    {
        $limits = $this->modelLimit->getLimits($activity_id, $now, $prize_id);

        if (!empty($limits)) {
            foreach ($limits as $limit) {

                // 从成功兑换表中获取该商品的数量
                $successNum = $this->modelExchange->getExchangeNum($activity_id, $identity_id, $prize_id, $limit['start_time']->sec, $limit['end_time']->sec);
                // 兑换数量>=限制时，无法兑换
                if (($successNum + $quantity) > $limit['limit'])
                    return false;

                // $exchanges = $this->modelExchange->filterExchangeByGroup($activity_id, $identity_id, $limit['start_time']->sec, $limit['end_time']->sec);
                // if (!empty($exchanges)) {
                //     if (empty($limit['prize_id']) && $prize_id == 'all' && !empty($limit['limit'])) {
                //         if ($exchanges['all'] >= $limit['limit']) {
                //             return false;
                //         }
                //     } else {
                //         if (!empty($limit['prize_id'])) {
                //             if (isset($exchanges[$limit['prize_id']]) && !empty($limit['limit']) && $prize_id == $limit['prize_id']) {
                //                 if ($exchanges[$limit['prize_id']] >= $limit['limit']) {
                //                     return false;
                //                 }
                //             }
                //         }
                //     }
                // }
            }
        }

        return true;
    }
}
