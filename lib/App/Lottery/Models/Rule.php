<?php

namespace App\Lottery\Models;

class Rule extends \App\Common\Models\Lottery\Rule
{

    private $_rules = null;

    /**
     * 获取指定活动的全部抽奖规则
     *
     * @param string $activity_id             
     * @param int $now            
     * @param array $prize_ids           
     * @param array $exclude_prize_ids          
     */
    public function getRules($activity_id, $now, array $prize_ids = array(), array $exclude_prize_ids = array())
    {
        if ($this->_rules == null) {
            $now = \App\Common\Utils\Helper::getCurrentTime($now);
            $query = array(
                'activity_id' => $activity_id,
                'allow_start_time' => array(
                    '$lte' => $now
                ),
                'allow_end_time' => array(
                    '$gte' => $now
                )
            );

            if (!empty($prize_ids)) {
                $query['prize_id'] = array(
                    '$in' => $prize_ids
                );
            }

            if (!empty($exclude_prize_ids)) {
                $query['prize_id'] = array(
                    '$nin' => $exclude_prize_ids
                );
            }

            $this->_rules = $this->findAll($query);
        }
        return $this->doShuffle($this->_rules);
    }

    /**
     * 对于概率进行随机分组处理
     *
     * @param array $list            
     * @return array
     */
    private function doShuffle($list)
    {
        $groupList = array();
        // 按照allow_probability分组
        array_map(function ($row) use (&$groupList) {
            $groupList[$row['allow_probability']][] = $row;
        }, $list);

        // 按照概率从高到底的次序排序
        ksort($groupList, SORT_NUMERIC);

        // 按分组随机排序
        $resultList = array();
        foreach ($groupList as $key => $rows) {
            shuffle($rows);
            $resultList = array_merge($resultList, $rows);
        }
        return $resultList;
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
     * 更新奖品的剩余数量
     *
     * @param array $rule            
     * @return bool false表示错误 true表示正确
     */
    public function updateRemain($rule)
    {
        $query = array(
            '_id' => $rule['_id'],
            'prize_id' => $rule['prize_id'],
            'win_number' => $rule['win_number'],
            'allow_number' => array(
                '$gt' => 0
            )
        );
        $updateData = array(
            '$inc' => array(
                'allow_number' => -1,
                'win_number' => 1
            )
        );
        $affectRows = $this->update($query, $updateData);
        if ($affectRows < 1) {
            throw new \Exception('竞争争夺奖品失败');
        } else {
            return true;
        }
    }

    /**
     * 生成抽奖概率
     *
     * @param string $activity_id            
     * @param string $prize_id            
     * @param number $allow_number            
     * @param number $allow_probability            
     * @param int $allow_start_time            
     * @param int $allow_end_time            
     */
    public function create($activity_id, $prize_id, $allow_number = 0, $allow_probability = 0, $allow_start_time = 0, $allow_end_time = 0)
    {
        if (empty($allow_start_time)) {
            $allow_start_time = \App\Common\Utils\Helper::getCurrentTime(strtotime('2016-01-01 00:00:00'));
        }
        if (empty($allow_end_time)) {
            $allow_end_time = \App\Common\Utils\Helper::getCurrentTime(strtotime('2099-12-31 23:59:59'));
        }
        $data = array();
        $data['activity_id'] = $activity_id;
        $data['prize_id'] = $prize_id;
        $data['allow_start_time'] = \App\Common\Utils\Helper::getCurrentTime($allow_start_time);
        $data['allow_end_time'] = \App\Common\Utils\Helper::getCurrentTime($allow_end_time);
        $data['allow_number'] = $allow_number;
        $data['allow_probability'] = $allow_probability;
        $data['win_number'] = 0;
        return $this->insert($data);
    }
}
