<?php

namespace App\Qyweixin\Models\ExternalContact;

class GroupChat extends \App\Common\Models\Qyweixin\ExternalContact\GroupChat
{

    /**
     * 根据客户ID获取信息
     *
     * @param string $chat_id            
     * @param string $authorizer_appid          
     */
    public function getInfoByChatId($chat_id, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['chat_id'] = $chat_id;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $agentid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncGroupChatList($authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        if (!empty($res['group_chat_list'])) {
            foreach ($res['group_chat_list'] as $group_chat_info) {
                $chat_id = $group_chat_info['chat_id'];
                $status = $group_chat_info['status'];

                $info = $this->getInfoByChatId($chat_id, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                $data['status'] = $status;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['chat_id'] = $chat_id;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateGroupChatInfoByApi($checkInfo, $groupChatInfo, $now)
    {
        if (!empty($groupChatInfo['errcode']) && in_array($groupChatInfo['errcode'], array(40050, 49008))) {
            $data = array();
            $data['is_exist'] = 0;
            $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
            return  $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
        }
        $authorizer_appid = $checkInfo['authorizer_appid'];
        $provider_appid = $checkInfo['provider_appid'];
        $agentid = $checkInfo['agentid'];
        $data = $this->getPrepareData($groupChatInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, $now);
        return  $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
    }

    private function getPrepareData($groupChatInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, $now)
    {
        $groupChatInfo = $groupChatInfo['group_chat'];
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "group_chat": {
         *      "chat_id": "wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA",
         *      "name": "销售客服群",
         *      "owner": "ZhuShengBen",
         *      "create_time": 1572505490,
         *      "notice" : "文明沟通，拒绝脏话",
         *      "member_list": [{
         *          "userid": "abel",
         *          "type": 1,
         *          "join_time": 1572505491,
         *          "join_scene": 1
         *       }, {
         *           "userid": "sam",
         *          "type": 1,
         *          "join_time": 1572505491,
         *           "join_scene": 1
         *      }, {
         *           "userid": "wmOgQhDgAAuXFJGwbve4g4iXknfOAAAA",
         *           "type": 2,
         *          "join_time": 1572505491,
         *          "join_scene": 1
         *      }],
         *      "admin_list": [{
         *      	"userid": "sam"
         *      }, {
         *      	"userid": "pony"
         *      }]
         *  }
         */
        if (empty($checkInfo)) {
            $data = array();
            $data['authorizer_appid'] = $authorizer_appid;
            $data['provider_appid'] = $provider_appid;
            $data['agentid'] = $agentid;
            $data['external_userid'] = isset($groupChatInfo['external_userid']) ? $groupChatInfo['external_userid'] : '';
            $data['name'] = isset($groupChatInfo['name']) ? $groupChatInfo['name'] : '';
            $data['owner'] = isset($groupChatInfo['owner']) ? $groupChatInfo['owner'] : '';
            $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($groupChatInfo['create_time']);
            $data['notice'] = isset($groupChatInfo['notice']) ? $groupChatInfo['notice'] : '';
            $data['member_list'] = isset($groupChatInfo['member_list']) ? \App\Common\Utils\Helper::myJsonEncode($groupChatInfo['member_list']) : '';
            $data['admin_list'] = isset($groupChatInfo['admin_list']) ? \App\Common\Utils\Helper::myJsonEncode($groupChatInfo['admin_list']) : '';
        } else {
            $data = array();
            if (isset($groupChatInfo['name'])) {
                $data['name'] = $groupChatInfo['name'];
            }
            if (isset($groupChatInfo['owner'])) {
                $data['owner'] = $groupChatInfo['owner'];
            }
            if (isset($groupChatInfo['create_time'])) {
                $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($groupChatInfo['create_time']);
            }
            if (isset($groupChatInfo['notice'])) {
                $data['notice'] = $groupChatInfo['notice'];
            }
            if (isset($groupChatInfo['member_list'])) {
                $data['member_list'] = \App\Common\Utils\Helper::myJsonEncode($groupChatInfo['member_list']);
            }
            if (isset($groupChatInfo['admin_list'])) {
                $data['admin_list'] = \App\Common\Utils\Helper::myJsonEncode($groupChatInfo['admin_list']);
            }
        }
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $data;
    }
}
