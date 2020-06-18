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
        $info = $this->getModel()
            ->where('follow_user', $follow_user)
            ->where('authorizer_appid', $authorizer_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function syncFollowUserList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['follow_user'])) {
            foreach ($res['follow_user'] as $follow_user) {
                $info = $this->getInfoByFollowUser($follow_user, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['get_time'] = date("Y-m-d H:i:s", $now);
                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['follow_user'] = $follow_user;
                    $this->insert($data);
                }
            }
        }
    }
}
