<?php

namespace App\Weixinredpack\Models;

class GotLog extends \App\Common\Models\Weixinredpack\GotLog
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
     * 记录数据
     *
     * @param string $mch_billno            
     * @param string $re_openid            
     * @param array $re_nickname            
     * @param array $re_headimgurl            
     * @param string $client_ip            
     * @param array $activity_id            
     * @param array $customer            
     * @param string $redpack_id            
     * @param int $total_num            
     * @param int $total_amount            
     * @param boolean $isNeedSendRedpack            
     * @param boolean $isOK            
     * @param int $try_count            
     * @param boolean $is_reissue            
     * @param string $memo            
     */
    public function record($mch_billno, $re_openid, $re_nickname, $re_headimgurl, $client_ip, $activity_id, $customer, $redpack_id, $total_num, $total_amount, $isNeedSendRedpack, $isOK = false, $try_count = 0, $is_reissue = false, array $memo = array())
    {
        if (empty($memo)) {
            $memo = array(
                'memo' => ""
            );
        }
        return $this->insert(array(
            'mch_billno' => $mch_billno,
            're_openid' => $re_openid,
            're_nickname' => $re_nickname,
            're_headimgurl' => $re_headimgurl,
            'client_ip' => $client_ip,
            'activity' => $activity_id,
            'customer' => $customer,
            'redpack' => $redpack_id,
            'total_num' => intval($total_num),
            'total_amount' => intval($total_amount),
            'got_time' => \App\Common\Utils\Helper::getCurrentTime(),
            'isOK' => $isOK,
            'try_count' => $try_count,
            'is_reissue' => $is_reissue,
            'isNeedSendRedpack' => $isNeedSendRedpack,
            'memo' => $memo
        ));
    }

    /**
     * 记录日志处理结果
     *
     * @param array $logInfo            
     * @param boolean $isOK            
     * @param array $errorLog            
     * @param array $memo            
     */
    public function updateIsOK($logInfo, $isOK, array $errorLog = array(), array $memo = array())
    {
        $data = array();
        $data['isOK'] = $isOK;
        if (!empty($memo)) {
            $data["memo"] = $memo;
        }
        if (!empty($errorLog)) {
            $data["error_logs"] = $errorLog;
        }
        $query = array(
            '_id' => $logInfo['_id']
        );
        $updateData = array(
            '$set' => $data
        );
        $affectRows = 0;
        if (!empty($updateData)) {
            $affectRows = $this->update($query, $updateData);
        }
        return $affectRows;
    }

    /**
     * 根据OpenID获取红包数量
     *
     * @param string $re_openid            
     * @param string $activity            
     * @param string $customer            
     * @param string $redpack            
     * @return number
     */
    public function getRedpackCountByOpenId($re_openid, $activity, $customer, $redpack, $start_time, $end_time)
    {
        $query = array(
            're_openid' => $re_openid,
            'activity' => $activity,
            'customer' => $customer,
            'redpack' => $redpack
        );
        if (!empty($start_time)) {
            $query['got_time']['$gte'] = \App\Common\Utils\Helper::getCurrentTime($start_time);
        }

        if (!empty($end_time)) {
            $query['got_time']['$lte'] = \App\Common\Utils\Helper::getCurrentTime($end_time);
        }

        return $this->count($query);
    }

    /**
     * 根据OpenID获取红包日志记录信息
     *
     * @param string $re_openid            
     * @param string $activity            
     * @param string $customer            
     * @param string $redpack            
     * @return number
     */
    public function getOneRedpackInfoByOpenId($re_openid, $activity, $customer, $redpack, $start_time, $end_time)
    {
        $query = array(
            're_openid' => $re_openid,
            'activity' => $activity,
            'customer' => $customer,
            'redpack' => $redpack
        );
        if (!empty($start_time)) {
            $query['got_time']['$gte'] = \App\Common\Utils\Helper::getCurrentTime($start_time);
        }

        if (!empty($end_time)) {
            $query['got_time']['$lte'] = \App\Common\Utils\Helper::getCurrentTime($end_time);
        }

        return $this->findOne($query);
    }

    public function incTryCount($_id, $trycount = 1, array $errorLog = array())
    {
        $query = array();
        $query['_id'] = ($_id);
        $updateData = array();
        $updateData['$inc'] = array(
            'try_count' => $trycount
        );
        if (!empty($errorLog)) {
            $updateData['$set'] = array(
                'error_logs' => $errorLog
            );
        }
        return $this->update($query, $updateData);
    }
}
