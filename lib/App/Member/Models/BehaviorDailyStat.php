<?php

namespace App\Member\Models;

class BehaviorDailyStat extends \App\Common\Models\Member\BehaviorDailyStat
{
    /**
     * 根据微信号获取信息
     *
     * @param string $openid
     * @param int $now 
     * @param string $act_type           
     * @return array
     */
    public function getInfoByOpenId($openid, $now, $act_type)
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";

        $query = array();
        $query['openid'] = trim($openid);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        $query['act_type'] = intval($act_type);

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param int $now 
     * @param string $act_type            
     * @return array
     */
    public function getInfoByMobile($mobile, $now, $act_type)
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";

        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        $query['act_type'] = intval($act_type);

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param int $now 
     * @param string $act_type            
     * @return array
     */
    public function getInfoByMobile4Lock($mobile, $now, $act_type)
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";
        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        $query['act_type'] = intval($act_type);
        $query['__FOR_UPDATE__'] = true;

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据memberid获取信息
     *
     * @param string $member_id
     * @param int $now 
     * @param string $act_type            
     * @return array
     */
    public function getInfoByMemberId($member_id, $now, $act_type)
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";
        $query = array();
        $query['member_id'] = trim($member_id);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        $query['act_type'] = intval($act_type);

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据微信号获取列表
     *
     * @param string $openid
     * @param int $now 
     * @param array $act_type_list           
     * @return array
     */
    public function getListByOpenId($openid, $now, $act_type_list = array())
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";

        $query = array();
        $query['openid'] = trim($openid);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        if (!empty($act_type_list)) {
            $query['act_type'] = array('$in' => $act_type_list);
        }
        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据手机号获取列表
     *
     * @param string $mobile
     * @param int $now 
     * @param array $act_type_list           
     * @return array
     */
    public function getListByMobile($mobile, $now, $act_type_list = array())
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";

        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        if (!empty($act_type_list)) {
            $query['act_type'] = array('$in' => $act_type_list);
        }
        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据memberid获取列表
     *
     * @param string $member_id
     * @param int $now 
     * @param array $act_type_list           
     * @return array
     */
    public function getListByMemberId($member_id, $now, $act_type_list = array())
    {
        $day = date("Y-m-d", $now);
        $day = $day . " 00:00:00";
        $query = array();
        $query['member_id'] = trim($member_id);
        $query['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
        if (!empty($act_type_list)) {
            $query['act_type'] = array('$in' => $act_type_list);
        }
        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 做统计记录
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
        $act_content_type,
        $act_content_subtype,
        $act_content_id,
        $act_daily_num
    ) {
        $act_num = intval($act_num);
        $info = $this->getInfoByMobile4Lock($mobile, $stat_time, $act_type);
        if (empty($info)) {
            $day = date("Y-m-d", $stat_time);
            $day = $day . " 00:00:00";
            $data = array();
            $data['act_type'] = intval($act_type);
            $data['member_id'] = trim($member_id);
            $data['mobile'] = trim($mobile);
            $data['openid'] = trim($openid);
            $data['act_time'] = \App\Common\Utils\Helper::getCurrentTime($day);
            $data['total_num'] = max($act_num, 0);
            $data['stat_time'] = \App\Common\Utils\Helper::getCurrentTime($stat_time);
            $data['act_content_list'] = \App\Common\Utils\Helper::myJsonEncode(array("{$act_content_type}_{$act_content_subtype}_{$act_content_id}"));
            $info = $this->insert($data);
        } else {
            $act_content_list = \json_decode($info['act_content_list'], true);
            if (empty($act_content_list)) {
                $act_content_list = array();
            }
            if ($act_num > 0) {
                // 增加
                $act_content_list[] = "{$act_content_type}_{$act_content_subtype}_{$act_content_id}";
                $query = array(
                    '_id' => $info['_id']
                );
                $updateData = array(
                    '$set' => array(
                        'stat_time' => \App\Common\Utils\Helper::getCurrentTime($stat_time),
                        'act_content_list' => \App\Common\Utils\Helper::myJsonEncode($act_content_list)
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
                // 删除
                $act_content_list2 = array();
                foreach ($act_content_list as $value) {
                    if ($value != "{$act_content_type}_{$act_content_subtype}_{$act_content_id}") {
                        $act_content_list2[] = $value;
                    }
                }
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
                        'stat_time' => \App\Common\Utils\Helper::getCurrentTime($stat_time),
                        'act_content_list' => \App\Common\Utils\Helper::myJsonEncode($act_content_list2)
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
