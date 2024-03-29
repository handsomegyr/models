<?php

namespace App\Qyweixin\Models\Contact;

class User extends \App\Common\Models\Qyweixin\Contact\User
{

    /**
     * @var \Qyweixin\Client
     */
    private $_qyweixin;

    public function setQyweixinInstance(\Qyweixin\Client $qyweixin)
    {
        $this->_qyweixin = $qyweixin;
    }

    /**
     * 获取用户信息
     *
     * @param string $userid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getInfoByUserId($userid, $authorizer_appid, $provider_appid, $agentid)
    {
        $info = $this->findOne(array(
            'userid' => $userid,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid
        ));
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

    /**
     * 通过活动授权更新用户个人信息
     *
     * @param string $userid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     * @param array $userInfo
     */
    public function updateUserInfoBySns($userid, $authorizer_appid, $provider_appid, $agentid, $userInfo)
    {
        $checkInfo = $this->getInfoByUserId($userid, $authorizer_appid, $provider_appid, $agentid);
        $data = $this->getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, time());
        if (!empty($checkInfo)) {
            $affectRows = $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
            return array_merge($checkInfo, $data);
        } else {
            if (empty($data['userid'])) {
                throw new \Exception('userid is empty');
            }
            return $this->insert($data);
        }
    }

    /**
     * 获取用户信息 最新有效的
     *
     * @param string $userid
     * @param string $authorizer_appid
     * @param string $provider_appid
     * @param string $agentid
     */
    public function getUserInfoByIdLastWeek($userid, $authorizer_appid, $provider_appid, $agentid, $now)
    {
        $info = $this->findOne(array(
            'userid' => $userid,
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid,
            'updated_at' => array('$gt' => \App\Common\Utils\Helper::getCurrentTime($now - 7 * 86400))
        ));
        return $info;
    }

    /**
     * 根据用户的互动行为，通过服务器端token获取该用户的个人信息
     * userid不存在或者随机100次执行一次更新用户信息
     */
    public function updateUserInfoByAction($userid, $authorizer_appid, $provider_appid, $agentid, $range = true)
    {
        if (empty($userid) || $userid == 'sys') {
            return array();
        }

        $checkInfo = $this->getInfoByUserId($userid, $authorizer_appid, $provider_appid, $agentid);
        // $range = (rand(0, 100) === 1);
        if (empty($checkInfo) || $range) { // || empty($checkInfo['subscribe'])
            try {
                $userInfo = $this->_qyweixin->getUserManager()->get($userid);
                // $e = new \Exception(\App\Common\Utils\Helper::myJsonEncode($userInfo));
                // $modelErrorLog = new \App\Activity\Models\ErrorLog();
                // $modelErrorLog->log(2, $e, time());
            } catch (\Exception $e) {
                $userInfo = array();
                $userInfo['userid'] = $userid;
                $userInfo['error_msg'] = $e->getMessage();
            }

            $data = $this->getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, time());

            if (!empty($checkInfo)) {
                $affectRows = $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
                return $userInfo;
            } else {
                if (empty($data['userid'])) {
                    throw new \Exception('userid is empty');
                }
                $checkInfo = $this->insert($data);
            }
        }
        return $checkInfo;
    }

    public function updateUserInfoById($checkInfo, $userInfo, $now)
    {
        // [errmsg] => userid not found, hint: [1646185148234890469598501], from ip: 115.29.169.68, more info at https://open.work.weixin.qq.com/devtool/query?e=60111
        if (!empty($userInfo['errcode']) && $userInfo['errcode'] == 60111) {
            $data = array();
            $data['is_exist'] = 0;
            $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
            return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
        }
        $authorizer_appid = $checkInfo['authorizer_appid'];
        $provider_appid = $checkInfo['provider_appid'];
        $agentid = $checkInfo['agentid'];
        $data = $this->getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, time());
        return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
    }

    public function syncUserList($authorizer_appid, $provider_appid, $agentid, $res, $now)
    {
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userlist": [
         * {
         *    "userid": "zhangsan",
         *    "name": "李四",
         *    "department": [1, 2],
         *    "open_userid": "xxxxxx"
         * }
         *]
         * }
         */
        if (!empty($res['userlist'])) {
            foreach ($res['userlist'] as $userInfo) {
                $userid = $userInfo['userid'];
                if (empty($userid)) {
                    continue;
                }
                $checkInfo = $this->getInfoByUserId($userid, $authorizer_appid, $provider_appid, $agentid);
                $data = $this->getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, $now);

                if (!empty($checkInfo)) {
                    return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
                } else {
                    if (empty($data['userid'])) {
                        throw new \Exception('userid is empty');
                    }
                    $checkInfo = $this->insert($data);
                }
            }
        }
    }

    private function getPrepareData($userInfo, $authorizer_appid, $provider_appid, $agentid, $checkInfo, $now)
    {
        /**
         * "errcode": 0, 
         * "errmsg": "ok", 
         * "userid": "GuoYongRong", 
         * "name": "郭永荣", 
         * "department": [ ], 
         * "position": "", 
         * "mobile": "13564100096", 
         * "gender": "1", 
         * "email": "", 
         * "avatar": "http://wework.qpic.cn/bizmail/3781P7vBiadFNmvYJic4sy6n3uDrPfwvea0J0rjbrcC9vQdGOMOAgICg/0", 
         * "status": 1, 
         * "isleader": 0, 
         * "extattr": {
         *     "attrs": [ ]
         * }, 
         * "telephone": "", 
         * "enable": 1, 
         * "hide_mobile": 0, 
         * "order": [ ], 
         * "main_department": 0, 
         * "qr_code": "https://open.work.weixin.qq.com/wwopen/userQRCode?vcode=vc2d24fd99a0f6208c", 
         * "alias": "", 
         * "is_leader_in_dept": [ ], 
         * "thumb_avatar": "http://wework.qpic.cn/bizmail/3781P7vBiadFNmvYJic4sy6n3uDrPfwvea0J0rjbrcC9vQdGOMOAgICg/100"
         */
        if (empty($checkInfo)) {
            $data = array();
            $data['authorizer_appid'] = $authorizer_appid;
            $data['provider_appid'] = $provider_appid;
            $data['agentid'] = $agentid;
            $data['userid'] = isset($userInfo['userid']) ? $userInfo['userid'] : '';
            $data['name'] = isset($userInfo['name']) ? $userInfo['name'] : '';
            $data['gender'] = isset($userInfo['gender']) ? $userInfo['gender'] : '0';
            $data['openid'] = isset($userInfo['openid']) ? $userInfo['openid'] : '';
            $data['alias'] = isset($userInfo['alias']) ? $userInfo['alias'] : '';
            $data['position'] = isset($userInfo['position']) ? $userInfo['position'] : '';
            $data['thumb_avatar'] = isset($userInfo['thumb_avatar']) ? $userInfo['thumb_avatar'] : '';
            $data['avatar'] = isset($userInfo['avatar']) ? $userInfo['avatar'] : '';
            $data['email'] = isset($userInfo['email']) ? $userInfo['email'] : '';
            $data['biz_mail'] = isset($userInfo['biz_mail']) ? $userInfo['biz_mail'] : '';
            $data['telephone'] = isset($userInfo['telephone']) ? $userInfo['telephone'] : '';
            $data['department'] = isset($userInfo['department']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['department']) : '';
            $data['enable'] = isset($userInfo['enable']) ? intval($userInfo['enable']) : 0;
            $data['to_invite'] = isset($userInfo['to_invite']) ? intval($userInfo['to_invite']) : 0;
            $data['status'] = isset($userInfo['status']) ? intval($userInfo['status']) : 0;
            $data['qr_code'] = isset($userInfo['qr_code']) ? $userInfo['qr_code'] : '';
            $data['is_leader_in_dept'] = isset($userInfo['is_leader_in_dept']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['is_leader_in_dept']) : '';
            $data['direct_leader'] = isset($userInfo['direct_leader']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['direct_leader']) : '';
            $data['department_order'] = isset($userInfo['order']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['order']) : '';
            $data['open_userid'] = isset($userInfo['open_userid']) ? $userInfo['open_userid'] : '';
            $data['hide_mobile'] = isset($userInfo['hide_mobile']) ? $userInfo['hide_mobile'] : '0';
            $data['english_name'] = isset($userInfo['english_name']) ? $userInfo['english_name'] : '';
            $data['mobile'] = isset($userInfo['mobile']) ? $userInfo['mobile'] : '';
            $data['avatar_mediaid_recid'] = isset($userInfo['avatar_mediaid_recid']) ? $userInfo['avatar_mediaid_recid'] : '0';
            $data['avatar_mediaid'] = isset($userInfo['avatar_mediaid']) ? $userInfo['avatar_mediaid'] : '';
            $data['extattr'] = isset($userInfo['extattr']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['extattr']) : '';
            $data['external_profile'] = isset($userInfo['external_profile']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['external_profile']) : '';
            $data['external_position'] = isset($userInfo['external_position']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['external_position']) : '';
            $data['address'] = isset($userInfo['address']) ? $userInfo['address'] : '';
            $data['main_department'] = isset($userInfo['main_department']) ? $userInfo['main_department'] : '';

            $data['session_key'] = isset($userInfo['session_key']) ? $userInfo['session_key'] : '';
            $data['oss_headimgurl'] = isset($userInfo['oss_headimgurl']) ? $userInfo['oss_headimgurl'] : '';
            $data['access_token'] = isset($userInfo['access_token']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['access_token']) : '';
        } else {
            $data = array();
            if (isset($userInfo['userid'])) {
                $data['userid'] = $userInfo['userid'];
            }
            if (isset($userInfo['name'])) {
                $data['name'] = $userInfo['name'];
            }
            if (isset($userInfo['gender'])) {
                $data['gender'] = $userInfo['gender'];
            }
            if (isset($userInfo['openid'])) {
                $data['openid'] = $userInfo['openid'];
            }
            if (isset($userInfo['alias'])) {
                $data['alias'] = $userInfo['alias'];
            }
            if (isset($userInfo['position'])) {
                $data['position'] = $userInfo['position'];
            }
            if (isset($userInfo['thumb_avatar'])) {
                $data['thumb_avatar'] = $userInfo['thumb_avatar'];
            }
            if (isset($userInfo['avatar'])) {
                $data['avatar'] = $userInfo['avatar'];
            }
            if (isset($userInfo['email'])) {
                $data['email'] = $userInfo['email'];
            }
            if (isset($userInfo['biz_mail'])) {
                $data['biz_mail'] = $userInfo['biz_mail'];
            }
            if (isset($userInfo['telephone'])) {
                $data['telephone'] = $userInfo['telephone'];
            }
            if (isset($userInfo['department'])) {
                $data['department'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['department']);
            }
            if (isset($userInfo['enable'])) {
                $data['enable'] = intval($userInfo['enable']);
            }
            if (isset($userInfo['to_invite'])) {
                $data['to_invite'] = intval($userInfo['to_invite']);
            }
            if (isset($userInfo['status'])) {
                $data['status'] = intval($userInfo['status']);
            }
            if (isset($userInfo['qr_code'])) {
                $data['qr_code'] = $userInfo['qr_code'];
            }
            if (isset($userInfo['is_leader_in_dept'])) {
                $data['is_leader_in_dept'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['is_leader_in_dept']);
            }
            if (isset($userInfo['direct_leader'])) {
                $data['direct_leader'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['direct_leader']);
            }
            if (isset($userInfo['order'])) {
                $data['department_order'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['order']);
            }
            if (isset($userInfo['access_token'])) {
                $data['access_token'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['access_token']);
            }
            if (isset($userInfo['open_userid'])) {
                $data['open_userid'] = $userInfo['open_userid'];
            }
            if (isset($userInfo['hide_mobile'])) {
                $data['hide_mobile'] = $userInfo['hide_mobile'];
            }
            if (isset($userInfo['english_name'])) {
                $data['english_name'] = $userInfo['english_name'];
            }
            if (isset($userInfo['mobile'])) {
                $data['mobile'] = $userInfo['mobile'];
            }
            if (isset($userInfo['session_key'])) {
                $data['session_key'] = $userInfo['session_key'];
            }
            if (isset($userInfo['oss_headimgurl'])) {
                $data['oss_headimgurl'] = $userInfo['oss_headimgurl'];
            }
            if (isset($userInfo['avatar_mediaid_recid'])) {
                $data['avatar_mediaid_recid'] = $userInfo['avatar_mediaid_recid'];
            }
            if (isset($userInfo['avatar_mediaid'])) {
                $data['avatar_mediaid'] = $userInfo['avatar_mediaid'];
            }
            if (isset($userInfo['extattr'])) {
                $data['extattr'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['extattr']);
            }
            if (isset($userInfo['external_profile'])) {
                $data['external_profile'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['external_profile']);
            }
            if (isset($userInfo['external_position'])) {
                $data['external_position'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['external_position']);
            }
            if (isset($userInfo['address'])) {
                $data['address'] = $userInfo['address'];
            }
            if (isset($userInfo['main_department'])) {
                $data['main_department'] = $userInfo['main_department'];
            }
        }
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $data;
    }
}
