<?php

namespace App\Qyweixin\Models\ExternalContact;

class UserBehaviorDataByUserid extends \App\Common\Models\Qyweixin\ExternalContact\UserBehaviorDataByUserid
{

    /**
     * 根据userid获取信息
     *
     * @param string $userid 
     * @param string $stat_time
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByUserId($userid, $stat_time, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['stat_time'] = \App\Common\Utils\Helper::getCurrentTime($stat_time);
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncBehaviorDataList($userid, $agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['behavior_data'])) {
            foreach ($res['behavior_data'] as $behavior_data_info) {
                $info = $this->getInfoByUserId($userid, $behavior_data_info['stat_time'], $agentid, $authorizer_appid, $provider_appid);
                $data = array();
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['chat_cnt'] = $behavior_data_info['chat_cnt'];
                $data['message_cnt'] = $behavior_data_info['message_cnt'];
                if (isset($behavior_data_info['reply_percentage'])) {
                    $data['reply_percentage'] = $behavior_data_info['reply_percentage'];
                }
                if (isset($behavior_data_info['avg_reply_time'])) {
                    $data['avg_reply_time'] = $behavior_data_info['avg_reply_time'];
                }
                $data['negative_feedback_cnt'] = $behavior_data_info['negative_feedback_cnt'];
                $data['new_apply_cnt'] = $behavior_data_info['new_apply_cnt'];
                $data['new_contact_cnt'] = $behavior_data_info['new_contact_cnt'];
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['userid'] = $userid;
                    $data['stat_time'] = \App\Common\Utils\Helper::getCurrentTime($behavior_data_info['stat_time']);
                    $this->insert($data);
                }
            }
        }
    }
}
