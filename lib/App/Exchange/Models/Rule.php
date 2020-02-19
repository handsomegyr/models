<?php

namespace App\Exchange\Models;

class Rule extends \App\Common\Models\Exchange\Rule
{

    public function getDefaultSort()
    {
        return $sort = array(
            'sort' => 1
        );
    }

    /**
     * 获取指定活动的全部可兑换奖品规则
     *
     * @param string $activity_id            
     * @param number $now            
     * @param number $score            
     * @param number $score_category            
     * @param array $prize_ids            
     */
    public function getRules($activity_id, $now, $score = 0, $score_category = 0, array $prize_ids = array())
    {
        $query = array();
        $query['activity_id'] = $activity_id;
        $query['allow_start_time'] = array(
            '$lte' => getCurrentTime($now)
        );
        $query['allow_end_time'] = array(
            '$gte' => getCurrentTime($now)
        );

        if (!empty($prize_ids)) {
            $query['prize_id'] = array(
                '$in' => $prize_ids
            );
        }

        if ($score) {
            $query['score'] = array(
                '$gte' => $score
            );
        }

        if ($score_category) {
            $query['score_category'] = $score_category;
        }

        $sort = $this->getDefaultSort();
        $list = $this->findAll($query, $sort);
        return $list;
    }

    /**
     * 锁住记录
     *
     * @param int $id            
     */
    public function lockRule($id)
    {
        $rule = $this->findOne(array(
            '_id' => $id,
            '__FOR_UPDATE__' => true
        ));
        return $rule;
    }

    /**
     * 兑换处理
     *
     * @param string $rule_id            
     * @param number $quantity            
     * @return bool false表示错误 true表示正确
     */
    public function exchange($rule_id, $quantity)
    {
        if ($quantity <= 0) {
            return false;
        }
        $quantity = abs($quantity);

        $query = array(
            '_id' => $rule_id,
            'allow_number' => array(
                '$gte' => $quantity
            )
        );
        $updateData = array(
            '$inc' => array(
                'allow_number' => -$quantity,
                'exchange_quantity' => $quantity
            )
        );
        $affectRows =  $this->update($query, $updateData);
        if ($affectRows < 1) {
            return false;
            // throw new \Exception("奖品的剩余数量已经为零");
        } else {
            return true;
        }
    }
}
