<?php

namespace App\Qyweixin\Models\ExternalContact;

class FollowUser extends \App\Common\Models\Qyweixin\ExternalContact\FollowUser
{

    /**
     * 根据成员名获取信息
     *
     * @param string $follow_user
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByFollowUser($follow_user, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['follow_user'] = $follow_user;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $agentid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncFollowUserList($authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        if (!empty($res['follow_user'])) {
            foreach ($res['follow_user'] as $follow_user) {
                $info = $this->getInfoByFollowUser($follow_user, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['follow_user'] = $follow_user;
                    $this->insert($data);
                }
            }
        }
    }
}
