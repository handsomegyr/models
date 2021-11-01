<?php

namespace App\Member\Models;

class BehaviorStat extends \App\Common\Models\Member\BehaviorStat
{
    /**
     * 根据微信号获取信息
     *
     * @param string $openid
     * @param string $act_type           
     * @return array
     */
    public function getInfoByOpenId($openid, $act_type)
    {
        $query = array();
        $query['openid'] = trim($openid);
        $query['act_type'] = intval($act_type);
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param string $act_type            
     * @return array
     */
    public function getInfoByMobile($mobile, $act_type)
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_type'] = intval($act_type);
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param string $act_type            
     * @return array
     */
    public function getInfoByMobile4Lock($mobile, $act_type)
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_type'] = intval($act_type);
        $query['__FOR_UPDATE__'] = true;
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据memberid获取信息
     *
     * @param string $member_id
     * @param string $act_type            
     * @return array
     */
    public function getInfoByMemberId($member_id, $act_type)
    {
        $query = array();
        $query['member_id'] = trim($member_id);
        $query['act_type'] = intval($act_type);
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据微信号获取列表
     *
     * @param string $openid
     * @param array $act_type_list           
     * @return array
     */
    public function getListByOpenId($openid, $act_type_list = array())
    {
        $query = array();
        $query['openid'] = trim($openid);

        if ($act_type_list) {
            $query['act_type'] = array(
                '$in' => $act_type_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据手机号获取列表
     *
     * @param string $mobile
     * @param array $act_type_list           
     * @return array
     */
    public function getListByMobile($mobile, $act_type_list = array())
    {
        $query = array();
        $query['mobile'] = trim($mobile);

        if ($act_type_list) {
            $query['act_type'] = array(
                '$in' => $act_type_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据memberid获取列表
     *
     * @param string $member_id
     * @param array $act_type_list           
     * @return array
     */
    public function getListByMemberId($member_id, $act_type_list = array())
    {
        $query = array();
        $query['member_id'] = trim($member_id);

        if ($act_type_list) {
            $query['act_type'] = array(
                '$in' => $act_type_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 做统计记录
     * 这个act_daily_num字段的含义是 一般情况下，act_daily_num字段的值是和act_num字段的值是一致
     * 只有当某些用户的行为每天是有次数限制的时候，两者的值是不一致的，比如说看视频每天最多5次，那么如果看第6次的时候 该值就为0
     * 
     * @return array
     */
    public function logStat(
        $act_type,
        $member_id,
        $mobile,
        $openid,
        $act_num,
        $stat_time,
        $act_daily_num
    ) {
        $act_num = intval($act_num);
        $act_daily_num = intval($act_daily_num);
        $info = $this->getInfoByMobile4Lock($mobile, $act_type);
        if (empty($info)) {
            $data = array();
            $data['act_type'] = intval($act_type);
            $data['member_id'] = trim($member_id);
            $data['mobile'] = trim($mobile);
            $data['openid'] = trim($openid);
            $data['total_num'] = max($act_num, 0);
            $data['total_complete_num'] = max($act_daily_num, 0);
            $data['stat_time'] = \App\Common\Utils\Helper::getCurrentTime($stat_time);
            $info = $this->insert($data);
        } else {
            if ($act_num > 0) {
                $query = array(
                    '_id' => $info['_id']
                );
                $updateData = array(
                    '$set' => array(
                        'stat_time' => \App\Common\Utils\Helper::getCurrentTime($stat_time)
                    ),
                    '$inc' => array(
                        'total_num' => $act_num
                    )
                );
                if ($act_daily_num > 0) {
                    $updateData['$inc']['total_complete_num'] = $act_daily_num;
                }
                $this->update($query, $updateData);
                // 再次获取
                $info = $this->getInfoById($info['_id']);
            } elseif ($act_num < 0) {
                $act_num = abs($act_num);
                $act_daily_num = abs($act_daily_num);
                $query = array(
                    '_id' => $info['_id'],
                    'total_num' => array(
                        '$gte' => $act_num
                    )
                );
                $updateData = array(
                    '$set' => array(
                        'stat_time' => \App\Common\Utils\Helper::getCurrentTime($stat_time)
                    ),
                    '$inc' => array(
                        'total_num' => -$act_num
                    )
                );
                if ($act_daily_num > 0) {
                    if (intval($info['total_complete_num']) >= $act_daily_num) {
                        $updateData['$inc']['total_complete_num'] = -$act_daily_num;
                    }
                }
                $this->update($query, $updateData);
                // 再次获取
                $info = $this->getInfoById($info['_id']);
            }
        }
        return $info;
    }
}
