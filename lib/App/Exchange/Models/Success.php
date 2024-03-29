<?php

namespace App\Exchange\Models;

/**
 * 废弃
 */
class Success extends \App\Common\Models\Exchange\Success
{

    /**
     * 增加成功日志记录
     *
     * @param string $user_id            
     * @param string $prize_id            
     * @param int $quantity            
     * @param int $score            
     * @param array $rule_info            
     * @param array $prize_code_info            
     * @param array $prize_info            
     * @param array $user_info            
     * @param array $user_contact            
     * @param array $memo            
     * @return array
     */
    public function addSuccess($user_id, $prize_id, $quantity, $score, array $rule_info, array $prize_code_info = array(), array $prize_info = array(), array $user_info = array(), array $user_contact = array(), array $memo = array('memo' => ''))
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['prize_id'] = $prize_id;
        $data['quantity'] = $quantity;
        $data['score'] = $score;
        $data['is_valid'] = true;
        $data['exchange_time'] = \App\Common\Utils\Helper::getCurrentTime();

        $data['rule_id'] = $rule_info['_id'];

        $data['prize_code'] = $prize_info['prize_code'];
        $data['prize_name'] = $prize_info['prize_name'];
        $data['prize_category'] = $prize_info['category'];
        $data['prize_is_virtual'] = $prize_info['is_virtual'];
        $data['prize_virtual_currency'] = $prize_info['virtual_currency'];

        if (!empty($prize_code_info)) {
            $data['prize_virtual_code'] = $prize_code_info['code'];
            $data['prize_virtual_pwd'] = $prize_code_info['pwd'];
        }
        if (!empty($user_info)) {
            $data['user_name'] = $user_info['user_name'];
            $data['user_headimgurl'] = $user_info['user_headimgurl'];
        }
        if (!empty($user_contact)) {
            $data['contact_name'] = $user_contact['name'];
            $data['contact_mobile'] = $user_contact['mobile'];
            $data['contact_address'] = $user_contact['address'];
        }

        $data['memo'] = $memo;
        $data = $this->insert($data);
        return $data;
    }

    /**
     * 获取某用户某奖品的兑换次数
     *
     * @param string $user_id            
     * @param string $prize_id            
     * @param int $start_time            
     * @param int $end_time            
     * @return number
     */
    public function getExchangeNum($user_id, $prize_id, $start_time, $end_time)
    {
        $count = 0;
        $query = array(
            'user_id' => $user_id,
            'prize_id' => $prize_id,
            'is_valid' => true,
            'exchange_time' => array(
                '$gte' => \App\Common\Utils\Helper::getCurrentTime($start_time),
                '$lt' => \App\Common\Utils\Helper::getCurrentTime($end_time)
            )
        );
        $list = $this->findAll($query);
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                $count += $val['quantity'];
            }
        }
        return $count;
    }

    /**
     * 更新用户联系信息
     *
     * @param string $id            
     * @param array $userContackInfo            
     */
    public function updateUserContackInfo($id, array $userContackInfo)
    {
        return $this->update(array(
            '_id' => $id
        ), array(
            '$set' => array(
                'contact_name' => $userContackInfo['name'],
                'contact_mobile' => $userContackInfo['mobile'],
                'contact_address' => $userContackInfo['address']
            )
        ));
    }

    /**
     * 获取兑换成功列表
     *
     * @param string $user_id            
     * @param int $start_time            
     * @param int $end_time            
     * @param array $prize_ids            
     * @return array
     */
    public function getExchangeList($user_id, $start_time = 0, $end_time = 0, array $prize_ids = array())
    {
        $query = array(
            'user_id' => $user_id
        );
        if (!empty($prize_ids)) {
            $query['prize_id'] = array(
                '$in' => $prize_ids
            );
        }
        $query['is_valid'] = true;
        if (!empty($start_time)) {
            $query['exchange_time']['$gte'] = \App\Common\Utils\Helper::getCurrentTime($start_time);
        }
        if (!empty($end_time)) {
            $query['exchange_time']['$lt'] = \App\Common\Utils\Helper::getCurrentTime($end_time);
        }
        $list = $this->findAll($query);
        return $list;
    }
}
