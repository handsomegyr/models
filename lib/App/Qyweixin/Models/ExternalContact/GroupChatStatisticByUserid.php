<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupChatStatisticByUserid extends \App\Common\Models\Qyweixin\ExternalContact\GroupChatStatisticByUserid
{

    /**
     * 根据userid获取信息
     * 
     * @param string $owner
     * @param string $day_begin_time
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByUserId($owner, $day_begin_time, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['owner'] = $owner;
        $query['day_begin_time'] = \App\Common\Utils\Helper::getCurrentTime($day_begin_time);
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncGroupchatStatisticList($day_begin_time, $authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        // "items": [{
        //     "owner": "zhangsan",
        //     "data": {
        //         "new_chat_cnt": 2,
        //         "chat_total": 2,
        //         "chat_has_msg": 0,
        //         "new_member_cnt": 0,
        //         "member_total": 6,
        //         "member_has_msg": 0,
        //         "msg_total": 0,
        //         "migrate_trainee_chat_cnt": 3
        //     }
        // }]
        if (!empty($res['items'])) {
            foreach ($res['items'] as $groupchatstatistic) {
                $owner = $groupchatstatistic['owner'];
                $statisticInfo = $groupchatstatistic['data'];
                $this->syncStatisticInfo($owner, $day_begin_time, $authorizer_appid, $provider_appid, $agentid, $statisticInfo, $now);
            }
        }
    }

    public function syncGroupchatStatisticListGroupByDay($owner, $authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        //     "items": [{
        //             "stat_time": 1600272000,
        //             "data": {
        //                 "new_chat_cnt": 2,
        //                 "chat_total": 2,
        //                 "chat_has_msg": 0,
        //                 "new_member_cnt": 0,
        //                 "member_total": 6,
        //                 "member_has_msg": 0,
        //                 "msg_total": 0,
        //                 "migrate_trainee_chat_cnt": 3
        //             }
        //         },
        //         {
        //             "stat_time": 1600358400,
        //             "data": {
        //                 "new_chat_cnt": 2,
        //                 "chat_total": 2,
        //                 "chat_has_msg": 0,
        //                 "new_member_cnt": 0,
        //                 "member_total": 6,
        //                 "member_has_msg": 0,
        //                 "msg_total": 0,
        //                 "migrate_trainee_chat_cnt": 3
        //             }
        //         }
        //     ]
        // }

        if (!empty($res['items'])) {
            foreach ($res['items'] as $groupchatstatistic) {
                $statisticInfo = $groupchatstatistic['data'];
                $day_begin_time = \App\Common\Utils\Helper::getCurrentTime($groupchatstatistic['stat_time']);
                $this->syncStatisticInfo($owner, $day_begin_time, $authorizer_appid, $provider_appid, $agentid, $statisticInfo, $now);
            }
        }
    }

    private function syncStatisticInfo($owner, $day_begin_time, $authorizer_appid, $provider_appid, $agentid, $statisticInfo, $now)
    {
        $info = $this->getInfoByUserId($owner, $day_begin_time, $authorizer_appid, $provider_appid, $agentid);
        $data = array();
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);

        $data['new_chat_cnt'] = $statisticInfo['new_chat_cnt'];
        $data['chat_total'] = $statisticInfo['chat_total'];
        if (isset($statisticInfo['chat_has_msg'])) {
            $data['chat_has_msg'] = $statisticInfo['chat_has_msg'];
        }
        if (isset($statisticInfo['new_member_cnt'])) {
            $data['new_member_cnt'] = $statisticInfo['new_member_cnt'];
        }
        $data['member_total'] = $statisticInfo['member_total'];
        $data['member_has_msg'] = $statisticInfo['member_has_msg'];
        $data['msg_total'] = $statisticInfo['msg_total'];
        if (isset($statisticInfo['migrate_trainee_chat_cnt'])) {
            $data['migrate_trainee_chat_cnt'] = $statisticInfo['migrate_trainee_chat_cnt'];
        }
        if (!empty($info)) {
            $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['provider_appid'] = $provider_appid;
            $data['authorizer_appid'] = $authorizer_appid;
            $data['agentid'] = $agentid;
            $data['day_begin_time'] = \App\Common\Utils\Helper::getCurrentTime($day_begin_time);
            $data['owner'] = $owner;
            $this->insert($data);
        }
    }
}
