<?php

namespace App\Weixinredpack\Models;

class Customer extends \App\Common\Models\Weixinredpack\Customer
{
    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => -1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 更新客户的总使用金额
     *
     * @param string $customer_id            
     * @param number $total_amount            
     */
    public function incUsedAmount($customer_id, $total_amount)
    {
        $total_amount = intval($total_amount);
        $query = array(
            '_id' => ($customer_id),
            'remain_amount' => array(
                '$gte' => $total_amount
            )
        );
        $updateData = array(
            '$inc' => array(
                'used_amount' => $total_amount,
                'remain_amount' => -$total_amount
            )
        );
        $affectRows = 0;
        if (!empty($updateData)) {
            $affectRows = $this->update($query, $updateData);
        }
        if ($affectRows < 1) {
            throw new \Exception("更新客户总红包金额的处理失败");
        }
        return $affectRows;
    }

    /**
     * 获取客户余额
     *
     * @return number
     */
    public function getRemainAmount(array $customerInfo)
    {
        return intval($customerInfo['remain_amount']);
    }
}
