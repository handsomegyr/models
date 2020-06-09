<?php

namespace App\Qyweixin\Models\Agent;

class Agent extends \App\Common\Models\Qyweixin\Agent\Agent
{
    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppid($provider_appid, $authorizer_appid, $agentid, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4AppId($provider_appid, $authorizer_appid, $agentid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);

        if ($is_get_latest || empty($application)) {
            $application = $this->findOne(array(
                'agentid' => $agentid,
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
    public function getTokenByAppid($provider_appid, $authorizer_appid, $agentid)
    {
        $token = $this->getInfoByAppid($provider_appid, $authorizer_appid, $agentid, true);
        if ($token == null) {
            return null;
        }
        try {
            $token = $this->refreshInfo($token);
        } catch (\Exception $e) {
        }

        return $token;
    }

    public function updateAccessToken($id, $access_token,  $expires_in, $memo = array())
    {
        $updateData = array();
        $updateData['access_token'] = $access_token;
        $updateData['access_token_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in);
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

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4AppId($provider_appid, $authorizer_appid, $agentid)
    {
        $cacheKey = "agent:provider_appid:{$provider_appid}:authorizer_appid:{$authorizer_appid}:agentid:{$agentid}";
        $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
        return $cacheKey;
    }

    private function refreshInfo($token)
    {
        if (empty($token['access_token_expire']) || strtotime($token['access_token_expire']) <= time()) {
            if (!empty($token['authorizer_appid']) && !empty($token['agentid'])) {
                $lockKey = cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__, $token['provider_appid'], $token['authorizer_appid'], $token['agentid']);
                $objLock = new \iLock($lockKey);
                if (!$objLock->lock()) {
                    $objToken = new \Qyweixin\Token\Server($token['agentid'], $token['secret']);
                    $arrToken = $objToken->getAccessToken();
                    if (!isset($arrToken['access_token'])) {
                        throw new \Exception(json_encode($arrToken));
                    }
                    $token = $this->updateAccessToken($token['_id'], $arrToken['access_token'], $arrToken['expires_in'], null);
                }
            }
        }

        // 缓存有效期不能超过token过期时间
        if ((time() + $this->_expire) > strtotime($token['access_token_expire'])) {
            $this->_expire = strtotime($token['access_token_expire']) - time();
        }

        return $token;
    }

    protected $_expire = 0;
}
