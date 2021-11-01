<?php

namespace App\Member\Models;

class BehaviorLog extends \App\Common\Models\Member\BehaviorLog
{

    /**
     * 根据微信号获取信息
     *
     * @param string $openid
     * @param string $act_type
     * @param string $act_content_type
     * @param string $act_content_subtype
     * @param string $act_content_id           
     * @return array
     */
    public function getInfoByOpenId($openid, $act_type, $act_content_type = "", $act_content_subtype = "", $act_content_id = "")
    {
        $query = array();
        $query['openid'] = trim($openid);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type)) {
            $query['act_content_type'] = trim($act_content_type);
        }
        if (!empty($act_content_subtype)) {
            $query['act_content_subtype'] = trim($act_content_subtype);
        }
        if (!empty($act_content_id)) {
            $query['act_content_id'] = trim($act_content_id);
        }

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param string $act_type
     * @param string $act_content_type
     * @param string $act_content_subtype
     * @param string $act_content_id            
     * @return array
     */
    public function getInfoByMobile($mobile, $act_type, $act_content_type = "", $act_content_subtype = "", $act_content_id = "")
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type)) {
            $query['act_content_type'] = trim($act_content_type);
        }
        if (!empty($act_content_subtype)) {
            $query['act_content_subtype'] = trim($act_content_subtype);
        }
        if (!empty($act_content_id)) {
            $query['act_content_id'] = trim($act_content_id);
        }

        $result = $this->findOne($query);
        return $result;
    }

    public function forceDelete($mobile, $act_type, $act_content_type = "", $act_content_subtype = "", $act_content_id = "")
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type)) {
            $query['act_content_type'] = trim($act_content_type);
        }
        if (!empty($act_content_subtype)) {
            $query['act_content_subtype'] = trim($act_content_subtype);
        }
        if (!empty($act_content_id)) {
            $query['act_content_id'] = trim($act_content_id);
        }

        $result = $this->physicalRemove($query);
        return $result;
    }

    /**
     * 根据memberid获取信息
     *
     * @param string $member_id
     * @param string $act_type
     * @param string $act_content_type
     * @param string $act_content_subtype
     * @param string $act_content_id            
     * @return array
     */
    public function getInfoByMemberId($member_id, $act_type, $act_content_type = "", $act_content_subtype = "", $act_content_id = "")
    {
        $query = array();
        $query['member_id'] = trim($member_id);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type)) {
            $query['act_content_type'] = trim($act_content_type);
        }
        if (!empty($act_content_subtype)) {
            $query['act_content_subtype'] = trim($act_content_subtype);
        }
        if (!empty($act_content_id)) {
            $query['act_content_id'] = trim($act_content_id);
        }

        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据微信号获取列表
     *
     * @param string $openid
     * @param string $act_type
     * @param array $act_content_type_list
     * @param array $act_content_subtype_list
     * @param array $act_content_id_list           
     * @return array
     */
    public function getListByOpenId($openid, $act_type, $act_content_type_list = array(), $act_content_subtype_list = array(), $act_content_id_list = array())
    {
        $query = array();
        $query['openid'] = trim($openid);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type_list)) {
            $query['act_content_type'] = array(
                '$in' => $act_content_type_list
            );
        }
        if (!empty($act_content_subtype_list)) {
            $query['act_content_subtype'] = array(
                '$in' => $act_content_subtype_list
            );
        }
        if (!empty($act_content_id_list)) {
            $query['act_content_id'] = array(
                '$in' => $act_content_id_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据手机号获取列表
     *
     * @param string $mobile
     * @param string $act_type
     * @param array $act_content_type_list
     * @param array $act_content_subtype_list
     * @param array $act_content_id_list           
     * @return array
     */
    public function getListByMobile($mobile, $act_type, $act_content_type_list = array(), $act_content_subtype_list = array(), $act_content_id_list = array())
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type_list)) {
            $query['act_content_type'] = array(
                '$in' => $act_content_type_list
            );
        }
        if (!empty($act_content_subtype_list)) {
            $query['act_content_subtype'] = array(
                '$in' => $act_content_subtype_list
            );
        }
        if (!empty($act_content_id_list)) {
            $query['act_content_id'] = array(
                '$in' => $act_content_id_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据memberid获取列表
     *
     * @param string $member_id
     * @param string $act_type
     * @param array $act_content_type_list
     * @param array $act_content_subtype_list
     * @param array $act_content_id_list           
     * @return array
     */
    public function getListByMemberId($member_id, $act_type, $act_content_type_list = array(), $act_content_subtype_list = array(), $act_content_id_list = array())
    {
        $query = array();
        $query['member_id'] = trim($member_id);
        $query['act_type'] = intval($act_type);
        if (!empty($act_content_type_list)) {
            $query['act_content_type'] = array(
                '$in' => $act_content_type_list
            );
        }
        if (!empty($act_content_subtype_list)) {
            $query['act_content_subtype'] = array(
                '$in' => $act_content_subtype_list
            );
        }
        if (!empty($act_content_id_list)) {
            $query['act_content_id'] = array(
                '$in' => $act_content_id_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 生成记录
     *
     * @return array
     */
    public function log(
        $act_type,
        $member_id,
        $mobile,
        $openid,
        $page_url,
        $scene,
        $btn_name,
        $ip,
        $act_time,
        $act_content_type,
        $act_content_subtype,
        $act_content_id,
        $activity_id,
        $invitor,
        array $memo = array('memo' => '')
    ) {
        $data = array();
        $data['act_type'] = intval($act_type);
        $data['member_id'] = trim($member_id);
        $data['mobile'] = trim($mobile);
        $data['openid'] = trim($openid);
        $data['page_url'] = trim($page_url);
        $data['scene'] = trim($scene);
        $data['btn_name'] = trim($btn_name);
        $data['ip'] = trim($ip);
        $data['act_content_type'] = trim($act_content_type);
        $data['act_content_subtype'] = trim($act_content_subtype);
        $data['act_content_id'] = trim($act_content_id);
        $data['invitor'] = trim($invitor);
        $data['activity_id'] = trim($activity_id);
        $data['act_time'] = \App\Common\Utils\Helper::getCurrentTime($act_time);
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }
}
