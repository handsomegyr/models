<?php

namespace App\Payment\Models;

class WeixinPayLog extends \App\Common\Models\Payment\WeixinPayLog
{
    public function log($data, $msg, $now, $is_refund = false, $refund_account = '', array $memo = array('memo' => ''))
    {
        $appid = isset($data['appid']) ? $data['appid'] : '';
        $bank_type = isset($data['bank_type']) ? $data['bank_type'] : '';
        $cash_fee = isset($data['cash_fee']) ? $data['cash_fee'] : 0;
        $fee_type = isset($data['fee_type']) ? $data['fee_type'] : '';
        $is_subscribe = isset($data['is_subscribe']) ? $data['is_subscribe'] : '';
        $mch_id = isset($data['mch_id']) ? $data['mch_id'] : '';
        $nonce_str = isset($data['nonce_str']) ? $data['nonce_str'] : '';
        $openid = isset($data['openid']) ? $data['openid'] : '';
        $out_trade_no = isset($data['out_trade_no']) ? $data['out_trade_no'] : '';
        $sign = isset($data['sign']) ? $data['sign'] : '';
        $time_end = isset($data['time_end']) ? $data['time_end'] : '';
        $total_fee = isset($data['total_fee']) ? $data['total_fee'] : 0;
        $trade_type = isset($data['trade_type']) ? $data['trade_type'] : '';
        $transaction_id = isset($data['transaction_id']) ? $data['transaction_id'] : '';
        $attach = isset($data['attach']) ? \json_decode($data['attach'], true) : array();

        $result_code = isset($data['result_code']) ? $data['result_code'] : '';
        $return_code = isset($data['return_code']) ? $data['return_code'] : '';
        $return_msg = isset($data['return_msg']) ? $data['return_msg'] : '';
        $err_code = isset($data['err_code']) ? $data['err_code'] : '';
        $err_code_des = isset($data['err_code_des']) ? $data['err_code_des'] : '';


        $device_info = isset($data['device_info']) ? $data['device_info'] : '';
        $cash_fee_type = isset($data['cash_fee_type']) ? $data['cash_fee_type'] : '';
        $sign_type = isset($data['sign_type']) ? $data['sign_type'] : '';
        $coupon_fee = isset($data['coupon_fee']) ? $data['coupon_fee'] : 0;
        $coupon_count = isset($data['coupon_count']) ? $data['coupon_count'] : 0;
        $settlement_total_fee = isset($data['settlement_total_fee']) ? $data['settlement_total_fee'] : 0;

        // 本地订单号
        $local_order_id = $attach['order_id'];
        // 付款方式
        $pay_method = $attach['pay_method'];

        $updateData = array();
        $updateData['appid'] = $appid;
        $updateData['bank_type'] = $bank_type;
        $updateData['cash_fee'] = $cash_fee;
        $updateData['fee_type'] = $fee_type;
        $updateData['is_subscribe'] = $is_subscribe;
        $updateData['mch_id'] = $mch_id;
        $updateData['nonce_str'] = $nonce_str;
        $updateData['openid'] = $openid;
        $updateData['sign'] = $sign;
        $updateData['time_end'] = $time_end;
        $updateData['total_fee'] = $total_fee;
        $updateData['trade_type'] = $trade_type;
        $updateData['transaction_id'] = $transaction_id;
        $updateData['attach'] = empty($data['attach']) ? '' : $data['attach'];
        $updateData['result_code'] = $result_code;
        $updateData['return_code'] = $return_code;
        $updateData['return_msg'] = $return_msg;
        $updateData['err_code'] = $err_code;
        $updateData['err_code_des'] = $err_code_des;

        $updateData['message'] = $msg;

        $updateData['local_order_id'] = $local_order_id;
        $updateData['pay_method'] = $pay_method;
        $updateData['notify_time'] = getCurrentTime($now);

        $updateData['is_refund'] = empty($is_refund) ? 0 : 1;
        $updateData['refund_account'] = $refund_account;

        $info = $this->getInfoByOutTradeNo($out_trade_no);
        if (empty($info)) {
            // insert
            $updateData['out_trade_no'] = $out_trade_no;
            $updateData['notify_num'] = 1;
            $updateData['memo'] = $memo;
            $this->insert($updateData);
        } else {
            // update
            $incData = array();
            $incData['notify_num'] = 1;
            if (!empty($memo)) {
                $updateData['memo'] = array_merge($info['memo'], $memo);
            }
            $this->update(array('_id' => $info['_id']), array(
                '$set' => $updateData,
                '$inc' => $incData
            ));
        }
    }

    /**
     * 根据Payid获取信息
     *
     * @param string $out_trade_no            
     * @return array
     */
    public function getInfoByOutTradeNo($out_trade_no)
    {
        $query = array();
        $query['out_trade_no'] = trim($out_trade_no);
        $info = $this->findOne($query);
        return $info;
    }
}
