<?php

namespace App\Components\Qyweixin\Services\Models\Auth;

namespace App\Qyweixin\Models\Auth;

class AuthCorp extends \App\Common\Models\Qyweixin\Auth\AuthCorp
{

    /**
     * 获取授权方企业的信息
     *
     * @return array
     */
    public function getInfoByAppid($provider_appid, $authorizer_appid, $corpid, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4AppId($provider_appid, $authorizer_appid, $corpid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);

        if ($is_get_latest || empty($application)) {
            $application = $this->findOne(array(
                'corpid' => $corpid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ));
            if (!empty($application)) {
                // 加缓存处理
                $expire_time = 5 * 60;
                $cache->save($cacheKey, $application, $expire_time);
            }
        }
        return $application;
    }

    /**
     * 获取有效的token信息
     *
     * @throws Exception
     * @return mixed array
     */
    public function getTokenByAppid($provider_appid, $authorizer_appid, $corpid)
    {
        $token = $this->getInfoByAppid($provider_appid, $authorizer_appid, $corpid, true);
        if ($token == null) {
            // throw new \Exception("xxxxxxxxxxxxxxx:{$provider_appid}:{$authorizer_appid}:{$corpid}");
            return null;
        }
        // try {
        $token = $this->refreshInfo($token);
        // } catch (\Exception $e) {
        // }

        return $token;
    }

    private function refreshInfo($token)
    {
        if (empty($token['access_token_expire']) || strtotime($token['access_token_expire']) <= time()) {
            if (!empty($token['authorizer_appid']) && !empty($token['corpid'])) {
                $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['provider_appid'], $token['authorizer_appid'], $token['corpid']);
                $objLock = new \iLock($lockKey);
                if (!$objLock->lock()) {
                    $corpid = empty($token['provider_appid']) ? $token['authorizer_appid'] : $token['provider_appid'];
                    $objToken = new \Qyweixin\Token\Server(corpid, $token['secret']);
                    $arrToken = $objToken->getAccessToken();
                    if (!isset($arrToken['access_token'])) {
                        throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($arrToken));
                    }
                    $token = $this->updateAccessToken($token['_id'], $arrToken['access_token'], $arrToken['expires_in']);
                }
            }
        }

        // 缓存有效期不能超过token过期时间
        if ((time() + $this->_expire) > strtotime($token['access_token_expire'])) {
            $this->_expire = strtotime($token['access_token_expire']) - time();
        }
        return $token;
    }

    public function updateAccessToken($id, $access_token, $expires_in, $memo = array())
    {
        $updateData = array();
        $updateData['access_token'] = $access_token;
        $updateData['access_token_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800);
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            $cache = $this->getDI()->get('cache');
            $cache->save($this->getCacheKey4AppId($newInfo['provider_appid'], $newInfo['authorizer_appid'], $newInfo['agentid']), $newInfo, $expire_time);
        }
        return $newInfo;
    }

    public function createAndUpdateAuthCorpInfo($provider_appid, $authorizer_appid, $access_token, $expires_in, $authCorpInfo, $memo = array())
    {
        //         {
        //             "corpid": 1000017, 
        //             "name": "企微助手", 
        //             "square_logo_url": "https://wework.qpic.cn/wwpic/809268_NV47ah_dT3OIIyZ_1661491101/0", 
        //             "privilege": {
        //                 "level": 0, 
        //                 "allow_party": [ ], 
        //                 "allow_user": [ ], 
        //                 "allow_tag": [ ], 
        //                 "extra_party": [ ], 
        //                 "extra_user": [ ], 
        //                 "extra_tag": [ ]
        //             }, 
        //             "is_customized_app": true
        //         }
        // auth_corp_info.corpid	授权方企业微信id
        // auth_corp_info.corp_name	授权方企业名称
        // auth_corp_info.corp_type	授权方企业类型，认证号：verified, 注册号：unverified
        // auth_corp_info.corp_square_logo_url	授权方企业方形头像
        // auth_corp_info.corp_user_max	授权方企业用户规模
        // auth_corp_info.corp_full_name	授权方企业的主体名称(仅认证或验证过的企业有)，即企业全称。企业微信将逐步回收该字段，后续实际返回内容为企业名称，即auth_corp_info.corp_name。
        // auth_corp_info.subject_type	企业类型，1. 企业; 2. 政府以及事业单位; 3. 其他组织, 4.团队号
        // auth_corp_info.verified_end_time	认证到期时间
        // auth_corp_info.corp_wxqrcode	授权企业在微信插件（原企业号）的二维码，可用于关注微信插件，二维码有效期为7天
        // auth_corp_info.corp_scale	企业规模。当企业未设置该属性时，值为空
        // auth_corp_info.corp_industry	企业所属行业。当企业未设置该属性时，值为空
        // auth_corp_info.corp_sub_industry	企业所属子行业。当企业未设置该属性时，值为空

        $corpid = $authCorpInfo['corpid'];
        $corp_name = $authCorpInfo['corp_name'];
        $corp_type = $authCorpInfo['corp_type'];
        $corp_square_logo_url = $authCorpInfo['corp_square_logo_url'];
        $corp_user_max = $authCorpInfo['corp_user_max'];
        $corp_full_name = $authCorpInfo['corp_full_name'];
        $subject_type = $authCorpInfo['subject_type'];
        $verified_end_time = $authCorpInfo['verified_end_time'];
        $corp_wxqrcode = $authCorpInfo['corp_wxqrcode'];
        $corp_scale = $authCorpInfo['corp_scale'];
        $corp_industry = $authCorpInfo['corp_industry'];
        $corp_sub_industry = $authCorpInfo['corp_sub_industry'];

        $lockKey = $this->getCacheKey4Appid($provider_appid, $authorizer_appid, $corpid);
        $objLock = new \iLock($lockKey);

        if (!$objLock->lock()) {
            $token = $this->getInfoByAppid($provider_appid, $authorizer_appid, $corpid);
            if (empty($token)) {
                // 创建
                $datas = array(
                    'provider_appid' => $provider_appid,
                    'authorizer_appid' => $authorizer_appid,
                    'corpid' => $corpid,
                    'corp_name' => $corp_name,
                    'corp_type' => $corp_type,
                    'corp_square_logo_url' => $corp_square_logo_url,
                    'access_token' => $access_token,
                    'access_token_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800),
                    'corp_user_max' => $corp_user_max,
                    'corp_full_name' => $corp_full_name,
                    'subject_type' => $subject_type,
                    'verified_end_time' => \App\Common\Utils\Helper::getCurrentTime($verified_end_time),
                    'corp_wxqrcode' => $corp_wxqrcode,
                    'corp_scale' => $corp_scale,
                    'corp_industry' => $corp_industry,
                    'corp_sub_industry' => $corp_sub_industry,
                    'memo' => $memo
                );
                return $this->insert($datas);
            } else {
                $memo = array_merge($token['memo'], $memo);
                $updateData = array();
                $updateData['corp_name'] = $corp_name;
                $updateData['corp_type'] = $corp_type;
                $updateData['corp_square_logo_url'] = $corp_square_logo_url;
                $updateData['access_token'] = $access_token;
                $updateData['access_token_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800);
                $updateData['corp_user_max'] = $corp_user_max;
                $updateData['corp_full_name'] = $corp_full_name;
                $updateData['subject_type'] = $subject_type;
                $updateData['verified_end_time'] = $verified_end_time;
                $updateData['corp_wxqrcode'] = $corp_wxqrcode;
                $updateData['corp_scale'] = $corp_scale;
                $updateData['corp_industry'] = $corp_industry;
                $updateData['corp_sub_industry'] = $corp_sub_industry;

                if (!empty($memo)) {
                    $updateData["memo"] = $memo;
                }
                $affectRows = $this->update(array('_id' => $token['_id']), array('$set' => $updateData));
                // 重新获取数据
                $newInfo = $this->getInfoById($token['_id']);
                if (!empty($newInfo)) {
                    $expire_time = 5 * 60;
                    $cache = $this->getDI()->get('cache');
                    $cache->save($this->getCacheKey4Appid($newInfo['provider_appid'], $newInfo['authorizer_appid'], $newInfo['corpid']), $newInfo, $expire_time);
                }

                return $newInfo;
            }
        }
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4Appid($provider_appid, $authorizer_appid, $corpid)
    {
        $cacheKey = $this->cache_tag . ":" . "authcorp:provider_appid:{$provider_appid}:authorizer_appid:{$authorizer_appid}:corpid:{$corpid}";
        return $cacheKey;
    }

    protected $_expire = 0;

    /**
     * 范围cache key字符串
     *
     * @return string
     */
    protected function cacheKey()
    {
        $args = func_get_args();
        return md5(serialize($args));
    }
}
