<?php

namespace App\Bargain\Models;

class Bargain extends \App\Common\Models\Bargain\Bargain
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'total_bargain_num' => -1,
            'worth' => -1,
            '_id' => -1
        );
        return $sort;
    }

    /**
     * 根据发起用户ID和砍价物编号和活动ID获取信息
     *
     * @param string $user_id            
     * @param string $bargain_code            
     * @param string $activity_id            
     * @param string $now            
     * @return array
     */
    public function getLatestInfoByUserIdAndBargainCode($user_id, $bargain_code, $activity_id, $now)
    {
        $query = array(
            'user_id' => $user_id,
            'code' => $bargain_code,
            'activity_id' => $activity_id
        );

        $now = \App\Common\Utils\Helper::getCurrentTime($now);
        $queryDefault = array(
            'is_closed' => false,
            'quantity' => array(
                '$gt' => 0
            ),
            'start_time' => array(
                '$lt' => $now
            ),
            'end_time' => array(
                '$gt' => $now
            )
        );

        $query = array_merge($query, $queryDefault);

        $sort = array(
            'launch_time' => -1
        );
        $list = $this->find($query, $sort, 0, 1);
        if (empty($list['datas'])) {
            return null;
        } else {
            return $list['datas'][0];
        }
    }

    /**
     * 生成砍价物数据
     *
     * @param string $activity_id            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $code            
     * @param string $name            
     * @param number $worth            
     * @param number $quantity            
     * @param number $bargain_from            
     * @param number $bargain_to            
     * @param number $worth_min            
     * @param number $bargain_max            
     * @param boolean $is_closed            
     * @param number $bargain_num_limit            
     * @param boolean $is_both_bargain            
     * @param number $bargain_period            
     * @param number $launch_time            
     * @param number $bargain_to_minworth_time            
     * @param string $memo            
     */
    public function create($activity_id, $user_id, $user_name, $user_headimgurl, $code, $name, $worth, $quantity, $bargain_from, $bargain_to, $worth_min, $bargain_max, $is_closed, $bargain_num_limit, $is_both_bargain, $start_time, $end_time, $bargain_period, $launch_time, $bargain_to_minworth_time, array $memo = array('memo' => ''))
    {
        return $this->insert(array(
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_headimgurl' => $user_headimgurl,
            'launch_time' => \App\Common\Utils\Helper::getCurrentTime($launch_time),
            'code' => $code,
            'name' => $name,
            'worth' => intval($worth),
            'current_worth' => intval($worth),
            'quantity' => intval($quantity),
            'bargain_from' => intval($bargain_from),
            'bargain_to' => intval($bargain_to),
            'worth_min' => intval($worth_min),
            'bargain_max' => intval($bargain_max),
            'is_closed' => $is_closed,
            'bargain_num_limit' => intval($bargain_num_limit),
            'is_both_bargain' => $is_both_bargain,
            'start_time' => \App\Common\Utils\Helper::getCurrentTime($start_time),
            'end_time' => \App\Common\Utils\Helper::getCurrentTime($end_time),
            'bargain_period' => $bargain_period,
            'total_bargain_num' => 0,
            'total_bargain_amount' => 0,
            'is_bargain_to_minworth' => false,
            'bargain_to_minworth_time' => \App\Common\Utils\Helper::getCurrentTime($bargain_to_minworth_time),
            'bargain_time' => \App\Common\Utils\Helper::getCurrentTime($launch_time),
            'close_time' => \App\Common\Utils\Helper::getCurrentTime(strtotime('0001-01-01 00:00:00')),
            'memo' => $memo
        ));
    }

    /**
     * 增加砍价总金额和砍价次数
     *
     * @param array $bargainInfo            
     * @param string $identity_id            
     * @param number $amount            
     * @param number $num               
     * @param number $now          
     * @throws Exception
     * @return array
     */
    public function incBargain($bargainInfo, $amount, $num, $now)
    {
        $bargain_id = ($bargainInfo['_id']);
        $query = array(
            '_id' => $bargainInfo['_id'],
            'current_worth' => array(
                '$gte' => $amount
            )
        );
        $updateData = array(
            '$inc' => array(
                'total_bargain_num' => $num,
                'total_bargain_amount' => $amount,
                'current_worth' => -$amount
            ),
            'set' => array(
                'bargain_time' => \App\Common\Utils\Helper::getCurrentTime($now)
            )
        );
        $affectRows = 0;
        if (!empty($updateData)) {
            $affectRows = $this->update($query, $updateData);
        }
        if ($affectRows < 1) {
            throw new \Exception("更新砍价物{$bargain_id}的砍价总金额和砍价次数的处理失败");
        }
        return $affectRows;
    }

    public function setBargainToMinworth($bargainInfo, $now)
    {
        // 如果砍到了最低价值的时候，设置一个标志位
        return $this->update(array(
            '_id' => $bargainInfo['_id']
        ), array(
            '$set' => array(
                'is_bargain_to_minworth' => true,
                'bargain_time' => \App\Common\Utils\Helper::getCurrentTime($now),
                'bargain_to_minworth_time' => \App\Common\Utils\Helper::getCurrentTime($now)
            )
        ));
    }

    /**
     * 下线处理
     *
     * @param string $id            
     */
    public function doClosed($id, $now)
    {
        $query = array(
            '_id' => ($id),
            'is_closed' => false
        );
        $updateData = array(
            '$set' => array(
                'is_closed' => true,
                'close_time' => \App\Common\Utils\Helper::getCurrentTime($now),
            )
        );
        $affectRows = 0;
        if (!empty($updateData)) {
            $affectRows = $this->update($query, $updateData);
        }
        if ($affectRows < 1) {
            throw new \Exception("更新砍价物{$id}的下线处理的处理失败");
        }
        return $affectRows;
    }
}
