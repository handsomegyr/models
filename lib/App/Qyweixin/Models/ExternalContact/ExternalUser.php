<?php

namespace App\Qyweixin\Models\ExternalContact;

class ExternalUser extends \App\Common\Models\Qyweixin\ExternalContact\ExternalUser
{

    /**
     * 根据客户名获取信息
     *
     * @param string $external_userid            
     * @param string $authorizer_appid          
     */
    public function getInfoByExternalUserid($external_userid, $authorizer_appid)
    {
        $info = $this->getModel()
            ->where('external_userid', $external_userid)
            ->where('authorizer_appid', $authorizer_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function syncExternalUserList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['external_userid'])) {
            foreach ($res['external_userid'] as $external_userid) {
                $info = $this->getInfoByExternalUserid($external_userid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['get_time'] = date("Y-m-d H:i:s", $now);
                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['external_userid'] = $external_userid;
                    $this->insert($data);
                }
            }
        }
    }
}
