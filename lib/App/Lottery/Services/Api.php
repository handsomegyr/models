<?php

namespace App\Lottery\Services;

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
     * @var \App\Lottery\Models\Exchange
     */
    protected $modelExchange = null;

    /**
     * @var \App\Lottery\Models\Limit
     */
    protected $modelLimit = null;

    /**
     * @var \App\Lottery\Models\Record
     */
    protected $modelRecord = null;

    /**
     * @var \App\Lottery\Models\Rule
     */
    protected $modelRule = null;

    protected $_isInstance = false;

    // 是否是简单抽奖,主要用于没有任何奖品数量限制的抽奖場景
    public $_isSimpleLottery = false;

    // 是否为了验证在并发环境下测试抽奖流程
    public $_isTest4Concurreny = false;

    // // 是否需要返回未领取或者未激活的中奖奖品
    public $is_need_return_unvalid_prize = false;

    public function __construct()
    {
        $this->modelCode = new \App\Prize\Models\Code();
        $this->modelExchange = new \App\Lottery\Models\Exchange();
        $this->modelLimit = new \App\Lottery\Models\Limit();
        $this->modelPrize = new \App\Prize\Models\Prize();
        $this->modelRecord = new \App\Lottery\Models\Record();
        $this->modelRule = new \App\Lottery\Models\Rule();
    }

    /**
     * 做抽奖动作
     *
     * @param string $activity_id            
     * @param string $identity_id            
     * @param number $now            
     * @param array $prize_ids
     *            如果传入一组特定的奖品的话,那么就是抽该组奖品
     * @param array $exclude_prize_ids
     *            如果传入一组特定排除的奖品的话,那么就是抽该组中的奖品
     * @param string $source            
     * @param array $user_info            
     * @param array $identityContact            
     * @param array $memo            
     */
    public function doLottery($activity_id, $identity_id, $now, array $prize_ids = array(), array $exclude_prize_ids = array(), $source = 'weixin', array $user_info = array(), $identityContact = array(), array $memo = array())
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );

        $rule_id = "";

        try {
            if ($this->_isInstance) {
                $ret['error_code'] = -100;
                $ret['error_msg'] = '每个抽奖实例只能执行一次doLottery方法，如需反复抽奖，请分别实例化Service_Api类';
                return $ret;
            }

            $this->_isInstance = true;

            // 控制活动流速，降低刷风险，同时只能有一个请求执行成功逻辑，将导致无法满足100%中奖要求
            $objLock = new \iLock(cacheKey($activity_id, $identity_id));
            if ($objLock->lock()) {
                $ret['error_code'] = -99;
                $ret['error_msg'] = '抽奖处于锁定状态，请稍后尝试';
                return $ret;
            }

            // 是否需要返回未领取或者未激活的中奖奖品
            if ($this->is_need_return_unvalid_prize) {
                // 检测是否存在未领取或者未激活的中奖奖品，有的话，再次让其 中同样的奖品完善个人信息。
                $invalidExchange = $this->modelExchange->getExchangeInvalidById($identity_id, $activity_id, $prize_ids);
                if (!empty($invalidExchange)) {
                    $rule_id = empty($invalidExchange['rule_id']) ? '' : $invalidExchange['rule_id'];
                    $invalidExchange['exchange_id'] = $invalidExchange['_id'];
                    $ret['result'] = $invalidExchange;
                    $this->modelRecord->record($activity_id, $identity_id, $source, 1, "未领取或者未激活的奖品再次中奖！", $rule_id, $invalidExchange['_id']);
                    return $ret;
                }
            }

            try {
                $this->modelExchange->begin();

                if ($this->_isTest4Concurreny) {
                    $rand = mt_rand(0, 100);
                    if ($rand < 10) {
                        throw new \Exception('随机异常发生2', -3);
                    }
                }
                // 检查中奖情况和中奖限制条件的关系
                $limit = $this->checkLimit($activity_id, $identity_id, $now, 'all');
                if ($limit == false) {
                    throw new \Exception('到达抽奖限制的上限制', -3);
                }

                // 检查中奖规则，检测用户是否中奖
                $rule = $this->lottery($activity_id, $identity_id, $now, $prize_ids, $exclude_prize_ids);
                if ($rule == false) {
                    throw new \Exception('很遗憾，您没有中奖', -4);
                }
                $rule_id = $rule['_id'];

                // 如果不是简单抽奖的话
                if (!$this->_isSimpleLottery) {
                    // LOCK
                    $rule = $this->modelRule->lockRule($rule_id);

                    // 更新中奖信息
                    $this->modelRule->updateRemain($rule);

                    if ($this->_isTest4Concurreny) {
                        $rand = mt_rand(0, 100);
                        if ($rand > 80) {
                            throw new \Exception('随机异常发生3', -3);
                        }
                    }
                }

                // throw new \Exception("测试", 999);
                // 竞争到奖品，根据奖品的属性标记状态                
                $prize_id = $rule['prize_id'];
                $prizeInfo = $this->modelPrize->getPrizeInfo($prize_id);
                if (empty($prizeInfo)) {
                    throw new \Exception("没有找到prize_id:{$prize_id}的奖品信息", -7);
                }

                $result = array();
                $result['identity_id'] = $identity_id;
                $result['prizeInfo'] = $prizeInfo;

                // 是否即时生效
                $isValid = !empty($prizeInfo['is_valid']) ? true : false;
                // 虚拟物品
                if (!empty($prizeInfo['is_virtual'])) {
                    // 发放虚拟奖品
                    if (!empty($prizeInfo['is_need_virtual_code'])) {
                        $code = $this->modelCode->getCode($rule['prize_id'], $now, $activity_id);
                        if (empty($code)) {
                            throw new \Exception('虚拟券不足!', -6);
                        }
                        if ($this->_isTest4Concurreny) {
                            $rand = mt_rand(0, 100);
                            if ($rand > 20 && $rand < 40) {
                                throw new \Exception('随机异常发生4', -3);
                            }
                        }
                    }
                } else {
                    // 如果是实物奖的话固定即时生效
                    $isValid = true;
                }

                // 记录中奖记录
                $prizeCode = !empty($code) ? $code : array();

                // 记录信息
                $exchangeInfo = $this->modelExchange->record($activity_id, $rule['prize_id'], $prizeInfo, $prizeCode, $identity_id, $user_info, $identityContact, $isValid, $source, $now, $rule_id, $memo);

                if (!empty($exchangeInfo)) {
                    $exchangeInfo['exchange_id'] = $exchangeInfo['_id'];
                } else {
                    throw new \Exception('中奖信息记录失败', -7);
                }

                if ($this->_isTest4Concurreny) {
                    $rand = mt_rand(0, 100);
                    if ($rand > 50 && $rand < 70) {
                        throw new \Exception('随机异常发生5', -3);
                    }
                }
                $this->modelExchange->commit();
                $this->modelRecord->record($activity_id, $identity_id, $source, 1, "恭喜您中奖了！", $rule_id, $exchangeInfo['_id']);
                $ret['result'] = $exchangeInfo;
            } catch (\Exception $e) {
                $this->modelExchange->rollback();
                $this->modelRecord->record($activity_id, $identity_id, $source, $e->getCode(), $e->getMessage(), $rule_id, 0);
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
    protected function checkLimit($activity_id, $identity_id, $now, $prize_id = 'all')
    {
        $limits = $this->modelLimit->getLimits($activity_id, $now);

        if (!empty($limits)) {
            foreach ($limits as $limit) {

                $exchanges = $this->modelExchange->filterExchangeByGroup($activity_id, $identity_id, $limit['start_time']->sec, $limit['end_time']->sec);

                if (!empty($exchanges)) {
                    if (empty($limit['prize_id']) && $prize_id == 'all' && !empty($limit['limit'])) {
                        if ($exchanges['all'] >= $limit['limit']) {
                            return false;
                        }
                    } else {
                        if (false && !empty($limit['prize_id']) && is_array($limit['prize_id']) && in_array($prize_id, $limit['prize_id'], true)) {
                            $exchangedTotalNumber = 0;
                            foreach ($exchanges as $k => $v) {
                                if (in_array($k, $limit['prize_id'], true)) {
                                    $exchangedTotalNumber += $v;
                                }
                            }
                            if ($exchangedTotalNumber >= $limit['limit']) {
                                return false;
                            }
                        } else {
                            if (!empty($limit['prize_id'])) {
                                if (isset($exchanges[$limit['prize_id']]) && !empty($limit['limit']) && $prize_id == $limit['prize_id']) {
                                    if ($exchanges[$limit['prize_id']] >= $limit['limit']) {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }


    /**
     * 计算抽奖概率判断用户是否中奖
     */
    protected function lottery($activity_id, $identity_id, $now, array $prize_ids = array(), array $exclude_prize_ids = array())
    {
        $rules = $this->modelRule->getRules($activity_id, $now, $prize_ids, $exclude_prize_ids);
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if (rand(0, 9999) < intval($rule['allow_probability']) && intval($rule['allow_number']) > 0) {
                    $allow = $this->checkLimit($activity_id, $identity_id, $now, $rule['prize_id']);
                    if ($allow)
                        return $rule;
                }
            }
        }
        return false;
    }
}
