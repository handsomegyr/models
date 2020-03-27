<?php

namespace App\Exchange\Models;

class Exchange extends \App\Common\Models\Exchange\Exchange
{

    private $_exchanges = null;

    /**
     * 检测当前信息是否存在
     *
     * @param string $user_id            
     * @param string $exchange_id            
     */
    public function checkExchangeBy($user_id, $exchange_id)
    {
        return $this->findOne(array(
            '_id' => ($exchange_id),
            'user_id' => $user_id
        ));
    }

    /**
     * 根据win_code检测当前信息是否存在
     *
     * @param string $user_id            
     * @param string $win_code            
     */
    public function checkExchangeByWincode($user_id, $win_code)
    {
        return $this->findOne(array(
            'win_code' => ($win_code),
            'user_id' => $user_id
        ));
    }

    /**
     * 获取指定用户的全部中奖纪录
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param number $startTime            
     * @param number $endTime            
     * @return NULL
     */
    public function getExchangeBy($user_id = '', $activity_id = '', $startTime = 0, $endTime = 0)
    {
        $query = array();
        if (!empty($user_id)) {
            $query['user_id'] = $user_id;
        }
        if (!empty($activity_id)) {
            $query['activity_id'] = $activity_id;
        }

        if (!empty($startTime)) {
            $query['got_time']['$gte'] = \App\Common\Utils\Helper::getCurrentTime($startTime);
        } else {
            // 防止中奖数据过多，增加条件只获取过去一年的数据
            $query['got_time']['$gte'] = \App\Common\Utils\Helper::getCurrentTime(strtotime(date("Y-m-d")) - 365 * 86400);
        }

        if (!empty($endTime)) {
            $query['got_time']['$lt'] = \App\Common\Utils\Helper::getCurrentTime($endTime);
        }
        $query['is_valid'] = true;

        $this->_exchanges = $this->findAll($query, array(
            '_id' => -1
        ));
        return $this->_exchanges;
    }

    /**
     * 获取之前已经中奖，但是未被确认掉的奖品
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param array $prize_ids            
     */
    public function getExchangeInvalidById($user_id, $activity_id, array $prize_ids = array())
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => $activity_id,
            'is_valid' => false
        );
        if (!empty($prize_ids)) {
            $query['prize_id'] = array(
                '$in' => $prize_ids
            );
        }
        return $this->findOne($query);
    }

    /**
     * 在全部数据结果中过滤有效数据
     *
     * @param string $activity_id            
     * @param string $user_id            
     * @param number $startTime            
     * @param number $endTime            
     * @return multitype:number
     */
    public function filterExchangeByGroup($activity_id, $user_id, $startTime = 0, $endTime = 0)
    {
        $rst = array();
        $exchanges = $this->getExchangeBy($user_id, $activity_id, $startTime, $endTime);
        if (!empty($exchanges)) {
            foreach ($exchanges as $key => $exchange) {
                if (!empty($exchange['prize_id'])) {
                    if (empty($rst[$exchange['prize_id']]))
                        $rst[$exchange['prize_id']] = 1;
                    else
                        $rst[$exchange['prize_id']] += 1;
                }
            }

            $rst['all'] = count($exchanges);
            return $rst;
        }

        return $rst;
    }

    /**
     * 记录数据
     *
     * @param string $activity_id            
     * @param string $prize_id            
     * @param array $prize_info            
     * @param array $prize_code            
     * @param string $user_id            
     * @param array $user_info            
     * @param array $user_contact            
     * @param string $isValid            
     * @param string $source             
     * @param string $got_time            
     * @param number $rule_id           
     * @param array $memo            
     */
    public function record($activity_id, $prize_id, $prize_info, $prize_code, $user_id, $user_info, $user_contact, $isValid, $source, $got_time, $quantity, $score_category, $score, $rule_id, array $memo = array())
    {
        $data = array(
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'prize_id' => $prize_id,
            'is_valid' => $isValid,
            'source' => $source,
            'got_time' => \App\Common\Utils\Helper::getCurrentTime($got_time)
        );

        $data['prize_code'] = $prize_info['prize_code'];
        $data['prize_name'] = $prize_info['prize_name'];
        $data['prize_category'] = $prize_info['category'];
        $data['prize_virtual_currency'] = $prize_info['virtual_currency'];
        $data['prize_is_virtual'] = $prize_info['is_virtual'];

        if (!empty($prize_code)) {
            $data['prize_virtual_code'] = $prize_code['code'];
            $data['prize_virtual_pwd'] = $prize_code['pwd'];
        } else {
            $data['prize_virtual_code'] = "";
            $data['prize_virtual_pwd'] = "";
        }

        if (!empty($user_info)) {
            $data['user_name'] = $user_info['user_name'];
            $data['user_headimgurl'] = $user_info['user_headimgurl'];
        } else {
            $data['user_name'] = "";
            $data['user_headimgurl'] = "";
        }

        if (!empty($user_contact)) {
            $data['contact_name'] = empty($user_contact['name']) ? "" : $user_contact['name'];
            $data['contact_mobile'] = empty($user_contact['mobile']) ? "" : $user_contact['mobile'];
            $data['contact_address'] = empty($user_contact['address']) ? "" : $user_contact['address'];
            $data['contact_province'] = empty($user_contact['province']) ? "" : $user_contact['province'];
            $data['contact_city'] = empty($user_contact['city']) ? "" : $user_contact['city'];
            $data['contact_district'] = empty($user_contact['district']) ? "" : $user_contact['district'];
            $data['contact_zipcode'] = empty($user_contact['zipcode']) ? "" : $user_contact['zipcode'];
        } else {
            $data['contact_name'] = "";
            $data['contact_mobile'] = "";
            $data['contact_address'] = "";
            $data['contact_province'] = "";
            $data['contact_city'] = "";
            $data['contact_district'] = "";
            $data['contact_zipcode'] = "";
        }
        $data['win_code'] = $this->createWinCode();

        $data['quantity'] = $quantity;
        $data['score_category'] = $score_category;
        $data['score'] = $score;
        $data['rule_id'] = $rule_id;

        $data['memo'] = $memo;
        return $this->insert($data);
    }

    /**
     * 更新兑换信息
     *
     * @param string $exchange_id            
     * @param array $info            
     */
    public function updateExchangeInfo($exchange_id, $info)
    {
        return $this->update(array(
            '_id' => $exchange_id
        ), array(
            '$set' => $info
        ));
    }

    /**
     * 获取中奖记录信息
     *
     * @param string $_id            
     */
    public function getExchangeInfoBy($exchange_id)
    {
        return $this->findOne(array(
            '_id' => $exchange_id
        ));
    }

    /**
     * 获取某用户某奖品的兑换次数
     *
     * @param string $activity_id 
     * @param string $user_id            
     * @param string $prize_id            
     * @param number $start_time            
     * @param number $end_time            
     * @return number
     */
    public function getExchangeNum($activity_id, $user_id, $prize_id, $start_time, $end_time)
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => $activity_id,
            'got_time' => array(
                '$gte' => \App\Common\Utils\Helper::getCurrentTime($start_time),
                '$lt' => \App\Common\Utils\Helper::getCurrentTime($end_time)
            ),
            'prize_id' => $prize_id,
            'is_valid' => true
        );
        $fields = array(
            'quantity'
        );
        $groups = array();
        $summary = 0;
        $ret = $this->sum($query, $fields, $groups);
        foreach ($ret as $row) {
            $summary += $row['quantity'];
        }
        return $summary;
    }

    /**
     * 获取中奖次数
     *
     * @param array $activity_ids            
     * @param array $user_ids            
     * @param array $prizeIds            
     * @param boolean $is_today            
     * @param array $conditions            
     * @return number
     */
    public function getExchangeCount(array $activity_ids, array $user_ids, array $prizeIds, $is_today = false, array $conditions = array())
    {
        $query = array();
        if ($activity_ids) {
            $query['activity_id'] = array(
                '$in' => $activity_ids
            );
        }

        if ($user_ids) {
            $query['user_id'] = array(
                '$in' => $user_ids
            );
        }
        if ($prizeIds) {
            $query['prize_id'] = array(
                '$in' => $prizeIds
            );
        }

        if ($is_today) {
            // 当天
            $today = date('Y-m-d');
            $start = \App\Common\Utils\Helper::getCurrentTime(strtotime($today . ' 00:00:00'));
            $end = \App\Common\Utils\Helper::getCurrentTime(strtotime($today . ' 23:59:59'));
            $query['got_time'] = array(
                '$gte' => $start,
                '$lte' => $end
            );
        }
        if ($conditions) {
            $query = array_merge($query, $conditions);
        }
        $num = $this->count($query);
        return $num;
    }

    /**
     * 根据各种条件获取中奖列表
     *
     * @param array $activity_ids            
     * @param array $user_ids            
     * @param array $prizeIds            
     * @param boolean $is_today            
     * @param array $conditions            
     * @return array
     */
    public function getExchangeList(array $activity_ids, array $user_ids, array $prizeIds, $is_today = false, array $conditions = array())
    {
        $query = array();
        if ($activity_ids) {
            $query['activity_id'] = array(
                '$in' => $activity_ids
            );
        }

        if ($user_ids) {
            $query['user_id'] = array(
                '$in' => $user_ids
            );
        }
        if ($prizeIds) {
            $query['prize_id'] = array(
                '$in' => $prizeIds
            );
        }

        if ($is_today) { // 当天
            $today = date('Y-m-d');
            $start = \App\Common\Utils\Helper::getCurrentTime(strtotime($today . ' 00:00:00'));
            $end = \App\Common\Utils\Helper::getCurrentTime(strtotime($today . ' 23:59:59'));
            $query['got_time'] = array(
                '$gte' => $start,
                '$lte' => $end
            );
        }
        if ($conditions) {
            $query = array_merge($query, $conditions);
        }

        $list = $this->findAll($query);
        return $list;
    }

    private function createWinCode()
    {
        $orderId = date('ymdHis') . (substr(microtime(true) * 10000, -3, 3)) . rand(1000, 9999);
        return $orderId;
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->_exchanges = null;
    }
}
