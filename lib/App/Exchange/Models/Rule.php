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
        $q = $this->getModel()
            ->where("activity_id", $activity_id)
            ->where("allow_start_time", "<=", date("Y-m-d H:i:s", $now))
            ->where("allow_end_time", ">=", date("Y-m-d H:i:s", $now));
        if (!empty($prize_ids)) {
            $q->whereIn("prize_id", $prize_ids);
        }

        if ($score) {
            $q->where("score", ">=", $score);
        }
        if ($score_category) {
            $q->where("score_category", $score_category);
        }
        $q->orderby("sort", "asc");

        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item;
            }
        }
        return $ret;
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

        $affectRows = $this->getModel()
            ->where("id", $rule_id)
            ->where("allow_number", ">=", 0)
            ->update(array(
                'allow_number' => DB::raw("allow_number-{$quantity}"),
                'exchange_quantity' => DB::raw("exchange_quantity+{$quantity}")
            ));
        if ($affectRows < 1) {
            return false;
            // throw new \Exception("奖品的剩余数量已经为零");
        } else {
            return true;
        }
    }


    // 减少规则数量
    public function exchange($rule_id, $quantity)
    {
        $option = array();
        $option['query'] = array(
            '_id' => $rule_id,
            'quantity' => array(
                '$gte' => $quantity
            )
        );
        $option['update'] = array(
            '$inc' => array(
                'quantity' => -$quantity,
                'exchange_quantity' => $quantity
            )
        );
        $rst = $this->findAndModify($option);
        if (empty($rst['ok'])) {
            throw new \Exception("减少规则数量的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("减少规则数量的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 获得可兑换奖品
     *
     * @param number $date            
     * @param number $score            
     * @param number $score_category            
     * @return array
     */
    public function getList($date = 0, $score = 0, $score_category = 0)
    {
        if (!$date) {
            $date = time();
        }
        $query = array();
        $query['start_time'] = array(
            '$lte' => getCurrentTime($date)
        );
        $query['end_time'] = array(
            '$gt' => getCurrentTime($date)
        );
        if ($score)
            $query['score'] = array(
                '$gte' => $score
            );
        if ($score_category)
            $query['score_category'] = $score_category;

        $sort = $this->getDefaultSort();
        $list = $this->findAll($query, $sort);
        return $list;
    }
}
