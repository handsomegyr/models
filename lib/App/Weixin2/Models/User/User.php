<?php

namespace App\Weixin2\Models\User;

class User extends \App\Common\Models\Weixin2\User\User
{

    private $_weixin;

    public function setWeixinInstance(\Weixin\Client $weixin)
    {
        $this->_weixin = $weixin;
    }

    /**
     * 获取用户信息
     *
     * @param string $openid            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByOpenId($openid, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'openid' => $openid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    /**
     * 通过活动授权更新用户个人信息
     *
     * @param string $openid            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     * @param array $userInfo            
     */
    public function updateUserInfoBySns($openid, $authorizer_appid, $component_appid, $userInfo)
    {
        $checkInfo = $this->getInfoByOpenId($openid, $authorizer_appid, $component_appid);
        $data = $this->getPrepareData($userInfo, $authorizer_appid, $component_appid, $checkInfo);
        if (!empty($checkInfo)) {
            $affectRows = $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
            return array_merge($checkInfo, $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * 获取用户信息 最新有效的
     *
     * @param string $openid            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getUserInfoByIdLastWeek($openid, $authorizer_appid, $component_appid, $now)
    {
        $info = $this->findOne(array(
            'openid' => $openid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' =>  $component_appid,
            'updated_at' => array('$gt' => \App\Common\Utils\Helper::getCurrentTime($now - 7 * 86400))
        ));
        return $info;
    }

    /**
     * 根据用户的互动行为，通过服务器端token获取该用户的个人信息
     * openid不存在或者随机100次执行一次更新用户信息
     */
    public function updateUserInfoByAction($openid, $authorizer_appid, $component_appid, $range = true)
    {
        $checkInfo = $this->getInfoByOpenId($openid, $authorizer_appid, $component_appid);
        // $range = (rand(0, 100) === 1);
        if (empty($checkInfo) || $range) { // || empty($checkInfo['subscribe'])
            try {
                $userInfo = $this->_weixin->getUserManager()->getUserInfo($openid);
            } catch (\Exception $e) {
                $userInfo = array();
                $userInfo['openid'] = $openid;
                $userInfo['error_msg'] = $e->getMessage();
            }

            $data = $this->getPrepareData($userInfo, $authorizer_appid, $component_appid, $checkInfo);

            if (!empty($checkInfo)) {
                $affectRows = $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
                return $userInfo;
            } else {
                $checkInfo = $this->insert($data);
            }
        }
        return $checkInfo;
    }

    public function updateUserInfoById($checkInfo, $userInfo)
    {
        $authorizer_appid = $checkInfo['authorizer_appid'];
        $component_appid = $checkInfo['component_appid'];
        $data = $this->getPrepareData($userInfo, $authorizer_appid, $component_appid, $checkInfo);
        return $this->update(array('_id' => $checkInfo['_id']), array('$set' => $data));
    }

    private function getPrepareData($userInfo, $authorizer_appid, $component_appid, $checkInfo)
    {
        if (empty($checkInfo)) {
            $data = array();
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['openid'] = isset($userInfo['openid']) ? $userInfo['openid'] : '';
            $data['nickname'] = isset($userInfo['nickname']) ? $userInfo['nickname'] : '';
            $data['sex'] = isset($userInfo['sex']) ? $userInfo['sex'] : '0';
            $data['language'] = isset($userInfo['language']) ? $userInfo['language'] : '';
            $data['city'] = isset($userInfo['city']) ? $userInfo['city'] : '';
            $data['province'] = isset($userInfo['province']) ? $userInfo['province'] : '';
            $data['country'] = isset($userInfo['country']) ? $userInfo['country'] : '';
            $data['headimgurl'] = isset($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '';
            $data['remark'] = isset($userInfo['remark']) ? $userInfo['remark'] : '';
            $data['groupid'] = isset($userInfo['groupid']) ? $userInfo['groupid'] : '';
            $data['tagid_list'] = isset($userInfo['tagid_list']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['tagid_list']) : '';
            $data['subscribe'] = isset($userInfo['subscribe']) ? intval($userInfo['subscribe']) : 0;
            $data['subscribe_time'] = isset($userInfo['subscribe_time']) ? \App\Common\Utils\Helper::getCurrentTime($userInfo['subscribe_time']) : '';
            $data['unionid'] = isset($userInfo['unionid']) ? $userInfo['unionid'] : '';
            $data['privilege'] = isset($userInfo['privilege']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['privilege']) : '';
            $data['access_token'] = isset($userInfo['access_token']) ? \App\Common\Utils\Helper::myJsonEncode($userInfo['access_token']) : '';
            $data['subscribe_scene'] = isset($userInfo['subscribe_scene']) ? $userInfo['subscribe_scene'] : '';
            $data['qr_scene'] = isset($userInfo['qr_scene']) ? $userInfo['qr_scene'] : '';
            $data['qr_scene_str'] = isset($userInfo['qr_scene_str']) ? $userInfo['qr_scene_str'] : '';
            $data['mobile'] = isset($userInfo['mobile']) ? $userInfo['mobile'] : '';
            $data['session_key'] = isset($userInfo['session_key']) ? $userInfo['session_key'] : '';
            $data['oss_headimgurl'] = isset($userInfo['oss_headimgurl']) ? $userInfo['oss_headimgurl'] : '';
        } else {
            $data = array();
            if (!empty($userInfo['nickname']) && ($checkInfo['nickname'] != $userInfo['nickname'])) {
                $data['nickname'] = $userInfo['nickname'];
            }
            if (isset($userInfo['sex'])) {
                $data['sex'] = $userInfo['sex'];
            }
            if (isset($userInfo['language'])) {
                $data['language'] = $userInfo['language'];
            }
            if (isset($userInfo['city'])) {
                $data['city'] = $userInfo['city'];
            }
            if (isset($userInfo['province'])) {
                $data['province'] = $userInfo['province'];
            }
            if (isset($userInfo['country'])) {
                $data['country'] = $userInfo['country'];
            }
            if (!empty($userInfo['headimgurl']) && ($checkInfo['headimgurl'] != $userInfo['headimgurl'])) {
                $data['headimgurl'] = $userInfo['headimgurl'];
            }
            if (isset($userInfo['remark'])) {
                $data['remark'] = $userInfo['remark'];
            }
            if (isset($userInfo['groupid'])) {
                $data['groupid'] = $userInfo['groupid'];
            }
            if (isset($userInfo['tagid_list'])) {
                $data['tagid_list'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['tagid_list']);
            }
            if (isset($userInfo['subscribe'])) {
                $data['subscribe'] = intval($userInfo['subscribe']);
            }
            if (isset($userInfo['subscribe_time'])) {
                $data['subscribe_time'] = \App\Common\Utils\Helper::getCurrentTime($userInfo['subscribe_time']);
            }
            if (!empty($userInfo['unionid']) && ($checkInfo['unionid'] != $userInfo['unionid'])) {
                $data['unionid'] = $userInfo['unionid'];
            }
            if (isset($userInfo['privilege'])) {
                $data['privilege'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['privilege']);
            }
            if (!empty($userInfo['access_token'])) {
                $data['access_token'] = \App\Common\Utils\Helper::myJsonEncode($userInfo['access_token']);
            }
            if (!empty($userInfo['subscribe_scene'])) {
                $data['subscribe_scene'] = $userInfo['subscribe_scene'];
            }
            if (!empty($userInfo['qr_scene'])) {
                $data['qr_scene'] = $userInfo['qr_scene'];
            }
            if (!empty($userInfo['qr_scene_str'])) {
                $data['qr_scene_str'] = $userInfo['qr_scene_str'];
            }
            if (!empty($userInfo['mobile']) && ($checkInfo['mobile'] != $userInfo['mobile'])) {
                $data['mobile'] = $userInfo['mobile'];
            }
            if (!empty($userInfo['session_key']) && ($checkInfo['session_key'] != $userInfo['session_key'])) {
                $data['session_key'] = $userInfo['session_key'];
            }
            if (!empty($userInfo['oss_headimgurl']) && ($checkInfo['oss_headimgurl'] != $userInfo['oss_headimgurl'])) {
                $data['oss_headimgurl'] = $userInfo['oss_headimgurl'];
            }
        }
        return $data;
    }
}
