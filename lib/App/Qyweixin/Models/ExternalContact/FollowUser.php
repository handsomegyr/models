<?php

namespace App\Qyweixin\Models\ExternalContact;

class FollowUser extends \App\Common\Models\Qyweixin\ExternalContact\FollowUser
{

    /**
     * 根据成员名获取信息
     *
     * @param string $follow_user            
     * @param string $authorizer_appid          
     */
    public function getInfoByFollowUser($follow_user, $authorizer_appid)
    {
        $query = array();
        $query['follow_user'] = $follow_user;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncFollowUserList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['follow_user'])) {
            foreach ($res['follow_user'] as $follow_user) {
                $info = $this->getInfoByFollowUser($follow_user, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['follow_user'] = $follow_user;
                    $this->insert($data);
                }
            }
        }
    }
}
