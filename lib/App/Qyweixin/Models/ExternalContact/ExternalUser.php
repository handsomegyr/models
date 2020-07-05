<?php

namespace App\Qyweixin\Models\ExternalContact;

class ExternalUser extends \App\Common\Models\Qyweixin\ExternalContact\ExternalUser
{
    /**
     * 根据客户ID获取信息
     *
     * @param string $external_userid            
     * @param string $authorizer_appid          
     */
    public function getInfoByExternalUserId($external_userid, $authorizer_appid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncExternalUserList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['external_userid'])) {
            foreach ($res['external_userid'] as $external_userid) {
                $info = $this->getInfoByExternalUserId($external_userid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['external_userid'] = $external_userid;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateExternalUserInfoByApi($checkInfo, $userInfo, $now)
    {
        $authorizer_appid = $checkInfo['authorizer_appid'];
        $provider_appid = $checkInfo['provider_appid'];
        $data = $this->getPrepareData($userInfo, $authorizer_appid, $provider_appid, $checkInfo);
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
    }

    private function getPrepareData($userInfo, $authorizer_appid, $provider_appid, $checkInfo)
    {
        $externalContactInfo = $userInfo['external_contact'];

        /**
         * {
         * "errcode": 0, 
         * "errmsg": "ok", 
         * "external_contact": {
         * "external_userid": "wmliEKBwAA_Gnuc2Ic8L6dha3-I60AEA", 
         * "name": "郭永荣", 
         * "type": 1, 
         * "avatar": "http://wx.qlogo.cn/mmhead/Q3auHgzwzM7VL9Oa0cRvP76blIib2xPmLqabg1amrX1UMHQUWXyZhWQ/0", 
         * "gender": 1, 
         * "unionid": "oWukX0uxZA_kGx-hkBJzTHSoSsPY"
         * }, 
         * "follow_user": [
         * {
         * "userid": "Ada", 
         * "remark": "", 
         * "description": "", 
         * "createtime": 1591605808, 
         * "tags": [ ], 
         * "remark_mobiles": [ ]
         * }
         * ]
         * }
         */
        if (empty($checkInfo)) {
            $data = array();
            $data['authorizer_appid'] = $authorizer_appid;
            $data['external_userid'] = isset($externalContactInfo['external_userid']) ? $externalContactInfo['external_userid'] : '';
            $data['name'] = isset($externalContactInfo['name']) ? $externalContactInfo['name'] : '';
            $data['avatar'] = isset($externalContactInfo['avatar']) ? $externalContactInfo['avatar'] : '';
            $data['type'] = isset($externalContactInfo['type']) ? intval($externalContactInfo['type']) : 0;
            $data['gender'] = isset($externalContactInfo['gender']) ? intval($externalContactInfo['gender']) : 0;
            $data['unionid'] = isset($externalContactInfo['unionid']) ? $externalContactInfo['unionid'] : '';
            $data['position'] = isset($externalContactInfo['position']) ? $externalContactInfo['position'] : '';
            $data['corp_name'] = isset($externalContactInfo['corp_name']) ? $externalContactInfo['corp_name'] : '';
            $data['corp_full_name'] = isset($externalContactInfo['corp_full_name']) ? $externalContactInfo['corp_full_name'] : '';
            $data['external_profile'] = isset($externalContactInfo['external_profile']) ? \json_encode($externalContactInfo['external_profile']) : '';
            $data['follow_user'] = isset($userInfo['follow_user']) ? \json_encode($userInfo['follow_user']) : '';
        } else {
            $data = array();
            $data['provider_appid'] = $provider_appid;
            if (isset($externalContactInfo['name'])) {
                $data['name'] = $externalContactInfo['name'];
            }
            if (isset($externalContactInfo['avatar'])) {
                $data['avatar'] = $externalContactInfo['avatar'];
            }
            if (isset($externalContactInfo['type'])) {
                $data['type'] = intval($externalContactInfo['type']);
            }
            if (isset($externalContactInfo['gender'])) {
                $data['gender'] = intval($externalContactInfo['gender']);
            }
            if (isset($externalContactInfo['unionid'])) {
                $data['unionid'] = $externalContactInfo['unionid'];
            }
            if (isset($externalContactInfo['position'])) {
                $data['position'] = $externalContactInfo['position'];
            }
            if (isset($externalContactInfo['corp_name'])) {
                $data['corp_name'] = $externalContactInfo['corp_name'];
            }
            if (isset($externalContactInfo['corp_full_name'])) {
                $data['corp_full_name'] = $externalContactInfo['corp_full_name'];
            }
            if (isset($externalContactInfo['external_profile'])) {
                $data['external_profile'] = \json_encode($externalContactInfo['external_profile']);
            }
            if (isset($userInfo['follow_user'])) {
                $data['follow_user'] = \json_encode($userInfo['follow_user']);
            }
        }
        return $data;
    }
}
