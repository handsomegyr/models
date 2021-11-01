<?php

namespace App\Member\Models;

class Task extends \App\Common\Models\Member\Task
{
    /**
     * 根据微信号获取信息
     *
     * @param string $openid
     * @param string $task_id           
     * @return array
     */
    public function getInfoByOpenId($openid, $task_id)
    {
        $result = $this->findOne(array(
            "openid" => trim($openid),
            "task_id" => trim($task_id)
        ));
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param string $task_id            
     * @return array
     */
    public function getInfoByMobile($mobile, $task_id)
    {
        $result = $this->findOne(array(
            "mobile" => trim($mobile),
            "task_id" => trim($task_id)
        ));
        return $result;
    }

    /**
     * 根据memberid获取信息
     *
     * @param string $member_id
     * @param string $task_id            
     * @return array
     */
    public function getInfoByMemberId($member_id, $task_id)
    {
        $result = $this->findOne(array(
            "member_id" => trim($member_id),
            "task_id" => trim($task_id)
        ));
        return $result;
    }
    /**
     * 生成记录
     *
     * @return array
     */
    public function log(
        $task_id,
        $member_id,
        $mobile,
        $openid,
        $complete_time,
        array $memo = array('memo' => '')
    ) {
        $data = array();
        $data['task_id'] = trim($task_id);
        $data['member_id'] = trim($member_id);
        $data['mobile'] = trim($mobile);
        $data['openid'] = trim($openid);
        $data['complete_time'] = \App\Common\Utils\Helper::getCurrentTime($complete_time);
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);

        return $info;
    }
}
