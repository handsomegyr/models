<?php

namespace App\Qyweixin\Models\ExternalContact;

class ExternalUser extends \App\Common\Models\Qyweixin\ExternalContact\ExternalUser
{
    /**
     * 根据客户ID获取信息
     *
     * @param string $external_userid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByExternalUserId($external_userid, $authorizer_appid, $provider_appid, $agentid)
    {
        $query = array();
        $query['external_userid'] = $external_userid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $query['agentid'] = $agentid;
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

    public function syncExternalUserList($authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        if (!empty($res['external_userid'])) {
            foreach ($res['external_userid'] as $external_userid) {
                $info = $this->getInfoByExternalUserId($external_userid, $authorizer_appid, $provider_appid, $agentid);
                $data = array();
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['provider_appid'] = $provider_appid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['agentid'] = $agentid;
                    $data['external_userid'] = $external_userid;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateExternalUserInfoByApi($checkInfo, $userInfo, $now)
    {
        // {"errcode":84061,"errmsg":"not external contact, hint: [1646594702057801817545474], from ip: 115.29.169.68, more info at https://open.work.weixin.qq.com/devtool/query?e=84061","follow_user":[]}
        if (!empty($userInfo['errcode']) && $userInfo['errcode'] == 84061) {
            $data = array();
            // 通过这个字段来表明企业微信那边有这条记录
            $data['is_exist'] = 1;
            $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
            return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
        }
        $authorizer_appid = $checkInfo['authorizer_appid'];
        $provider_appid = $checkInfo['provider_appid'];
        $agentid = $checkInfo['agentid'];
        $data = $this->getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, $now);
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
    }

    private function getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, $now)
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
            $data['agentid'] = $agentid;
            $data['authorizer_appid'] = $authorizer_appid;
            $data['provider_appid'] = $provider_appid;
            $data['external_userid'] = isset($externalContactInfo['external_userid']) ? $externalContactInfo['external_userid'] : '';
            $data['name'] = isset($externalContactInfo['name']) ? $externalContactInfo['name'] : '';
            $data['avatar'] = isset($externalContactInfo['avatar']) ? $externalContactInfo['avatar'] : '';
            $data['type'] = isset($externalContactInfo['type']) ? intval($externalContactInfo['type']) : 0;
            $data['gender'] = isset($externalContactInfo['gender']) ? intval($externalContactInfo['gender']) : 0;
            $data['unionid'] = isset($externalContactInfo['unionid']) ? $externalContactInfo['unionid'] : '';
            $data['position'] = isset($externalContactInfo['position']) ? $externalContactInfo['position'] : '';
            $data['corp_name'] = isset($externalContactInfo['corp_name']) ? $externalContactInfo['corp_name'] : '';
            $data['corp_full_name'] = isset($externalContactInfo['corp_full_name']) ? $externalContactInfo['corp_full_name'] : '';
            $data['external_profile'] = isset($externalContactInfo['external_profile']) ? \App\Common\Utils\Helper::myJsonEncode($externalContactInfo['external_profile']) : '';
            $data['follow_user'] = isset($userInfo['follow_user']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['follow_user']) : '';
        } else {
            $data = array();
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
                $data['external_profile'] = \App\Common\Utils\Helper::myJsonEncode($externalContactInfo['external_profile']);
            }
            if (isset($userInfo['follow_user'])) {
                $data['follow_user'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['follow_user']);
            }
        }
        $data['is_exist'] = 1;
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $data;
    }
}
