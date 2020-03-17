<?php

namespace App\Sign\Models;

class Sign extends \App\Common\Models\Sign\Sign
{

    /**
     * 根据user_id获取上一次的签到信息
     */
    public function getLastInfoByUserId($user_id, $activity_id)
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => $activity_id
        );
        $info = $this->find($query, array(
            'last_sign_time' => -1
        ), 0, 1);
        if (!empty($info['datas'])) {
            return $info['datas'][0];
        }
        return null;
    }

    /**
     * 判断签到时间
     * 1 连续签到，-1同天签到，0非连续签到
     */
    public function judgeSignTime($last_sign_time, $sign_time, $hour = 24)
    {
        // 如果最终签到日+$hour*60*60==$sign_time
        $last_sign_time = strtotime(date("Y-m-d", $last_sign_time) . " 00:00:00");
        $current_sign_time = strtotime(date("Y-m-d", $sign_time) . " 00:00:00");

        if (($last_sign_time + $hour * 60 * 60) == $current_sign_time) {
            // 24小时
            return 1; // 连续签到
        } elseif (($last_sign_time + $hour * 60 * 60) > $current_sign_time) {
            // 24小时内
            return -1; // 同天签到
        } elseif (($last_sign_time + $hour * 60 * 60) < $current_sign_time) {
            // 24小时外
            return 0; // 非连续签到
        }
    }

    /**
     * 处理签到
     *
     * @param string $activity_id            
     * @param string $user_id            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param number $sign_time              
     * @param string $ip          
     * @param string $valid_log_id            
     * @param number $judgeResult            
     * @param array $info            
     * @param array $memo            
     * @throws \Exception
     */
    public function process($activity_id, $user_id, $nickname, $headimgurl, $sign_time, $ip, $valid_log_id = '', $judgeResult = 1, $info = array(), $memo = array())
    {
        // 存在
        if (!empty($info)) {
            $query = array(
                "_id" => $info['_id']
            );
            // 连续的时候
            if ($judgeResult === 1) {
                $updateData = array(
                    "query" => array(
                        "_id" => $info['_id']
                    ),
                    "update" => array(
                        '$set' => array(
                            "lastip" => $ip,
                            "last_sign_time" => \App\Common\Utils\Helper::getCurrentTime($sign_time),
                            "is_continue_sign" => true,
                            'insameperiod_sign_count' => 1, // 连续签到则同一天的签到数重置
                            'valid_log_id' => $valid_log_id
                        ),
                        '$inc' => array(
                            'continue_sign_count' => 1, // 签到计数+1
                            'total_sign_count' => 1,
                            'total_sign_count2' => 1
                        )
                    )
                );
            } elseif ($judgeResult === 0) {
                // 非连续的时候
                $updateData = array(
                    "update" => array(
                        '$set' => array(
                            "is_continue_sign" => true,
                            "lastip" => $ip,
                            "continue_sign_count" => 1, // 重新计数连续签到
                            "restart_sign_time" => \App\Common\Utils\Helper::getCurrentTime($sign_time), // 重新设置签到日期
                            "last_sign_time" => \App\Common\Utils\Helper::getCurrentTime($sign_time),
                            'insameperiod_sign_count' => 1,
                            'valid_log_id' => $valid_log_id
                        ),
                        '$inc' => array(
                            'total_sign_count' => 1,
                            'total_sign_count2' => 1
                        )
                    )
                );
            } elseif ($judgeResult === -1) {
                // 同天签到
                $updateData = array(
                    "update" => array(
                        '$set' => array(
                            "lastip" => $ip,
                            "last_sign_time" => \App\Common\Utils\Helper::getCurrentTime($sign_time),
                            'valid_log_id' => $valid_log_id
                        ),
                        '$inc' => array(
                            'total_sign_count' => 1,
                            'insameperiod_sign_count' => 1
                        )
                    )
                );
            }
            $this->update($query, $updateData);
            $info = $this->getInfoById($info['_id']);
        } else {
            // 不存在
            $data = array();
            $data['activity_id'] = $activity_id; // 所属活动
            $data['user_id'] = $user_id; // 用户ID
            $data['nickname'] = $nickname; // 用户昵称
            $data['headimgurl'] = $headimgurl; // 用户头像

            $data['first_sign_time'] = \App\Common\Utils\Helper::getCurrentTime($sign_time); // 首次签到时间
            $data['restart_sign_time'] = \App\Common\Utils\Helper::getCurrentTime($sign_time); // 重新开始签到时间
            $data['last_sign_time'] = \App\Common\Utils\Helper::getCurrentTime($sign_time); // 最终签到时间

            $data['total_sign_count'] = 1; // 总签到数量（同天累加）
            $data['total_sign_count2'] = 1; // 总签到数量2（同天不累加）
            $data['insameperiod_sign_count'] = 1; // 同一天签到次数

            $data['continue_sign_count'] = 1; // 连续签到数量
            $data['is_continue_sign'] = true; // 是否连续签到
            $data['is_do'] = false; // 是否完成

            $data['lastip'] = $ip; // 最终IP
            $data['valid_log_id'] = $valid_log_id; // 有效的签到日志记录ID

            $data['memo'] = $memo;
            $info = $this->insert($data);
        }
        return $info;
    }
}
