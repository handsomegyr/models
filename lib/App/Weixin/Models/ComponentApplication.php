<?php

namespace App\Weixin\Models;

/**
 * @deprecated
 */
class ComponentApplication extends \App\Common\Models\Weixin\ComponentApplication
{

    private $_params = array();

    private $_expire = 30;

    /**
     * 开启token的缓存信息
     *
     * @param int $cacheTime            
     */
    public function setTokenCache($expire = 300)
    {
        $this->_expire = (int) $expire;
    }

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getApplicationInfoByAppId($appid)
    {
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $appid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if (empty($application)) {
            $application = $this->findOne(array(
                'appid' => $appid
            ));
            if (!empty($application)) {
                $expire_time = $this->getExpireTime($application);
                $cache->save($cacheKey, $application, $expire_time);
            }
        }
        return $application;
    }

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getApplicationInfoByAuthorizerAppId($appid)
    {
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $appid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if (empty($application)) {
            $application = $this->findOne(array(
                'authorizer_appid' => $appid
            ));
            if (!empty($application)) {
                $expire_time = $this->getExpireTime($application);
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
    public function getTokenByAppid($appid)
    {
        noLock:
        $token = $this->getApplicationInfoByAppId($appid);
        if ($token == null) {
            return null;
        }

        try {
            $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $appid);
            $token = $this->refreshInfo($cacheKey, $token);
        } catch (\Exception $e) {
        }

        return $token;
    }

    /**
     * 获取有效的token信息
     *
     * @throws Exception
     * @return mixed array
     */
    public function getTokenByAuthorizerAppid($appid)
    {
        noLock:
        $token = $this->getApplicationInfoByAuthorizerAppId($appid);
        if ($token == null) {
            return null;
        }
        try {
            $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $appid);
            $token = $this->refreshInfo($cacheKey, $token);
        } catch (\Exception $e) {
        }

        return $token;
    }

    public function updateAuthorizerAccessToken($info, $access_token, $refresh_token, $expires_in, $memo = array())
    {
        $cmd = array();
        $cmd['query'] = array(
            '_id' => $info['_id']
        );
        $data = array(
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'access_token_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + intval($expires_in))
        );
        if (empty($memo)) {
            $memo = array();
        }
        foreach ($memo as $key => $value) {
            $data["memo.{$key}"] = $value;
        }

        $cmd['update'] = array(
            '$set' => $data
        );
        $cmd['new'] = true;
        $cmd['upsert'] = true;
        $rst = $this->findAndModify($cmd);

        if (empty($rst['ok'])) {
            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
        }

        if (!empty($rst['value'])) {
            $cache = $this->getDI()->get('cache');
            $newInfo = $rst['value'];
            $expire_time = $this->getExpireTime($newInfo);
            // 更新缓存
            $appid = $newInfo['appid'];
            $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $appid);
            $cache->save($cacheKey, $newInfo, $expire_time);

            $authorizer_appid = $newInfo['authorizer_appid'];
            $cacheKey2 = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $authorizer_appid);
            $cache->save($cacheKey2, $newInfo, $expire_time);

            return $newInfo;
        } else {
            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
        }
    }

    public function updateComponentAccessToken($info, $component_access_token, $expires_in, $component_verify_ticket, $memo = array())
    {
        $cmd = array();
        $cmd['query'] = array(
            '_id' => $info['_id']
        );
        $data = array(
            'component_access_token' => $component_access_token,
            'component_access_token_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + intval($expires_in)),
            'component_verify_ticket' => $component_verify_ticket
        );
        if (empty($memo)) {
            $memo = array();
        }
        foreach ($memo as $key => $value) {
            $data["memo.{$key}"] = $value;
        }

        $cmd['update'] = array(
            '$set' => $data
        );
        $cmd['new'] = true;
        $cmd['upsert'] = true;
        $rst = $this->findAndModify($cmd);

        if (empty($rst['ok'])) {
            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
        }

        if (!empty($rst['value'])) {
            $cache = $this->getDI()->get('cache');
            $newInfo = $rst['value'];
            $expire_time = $this->getExpireTime($newInfo);
            // 更新缓存
            $appid = $newInfo['appid'];
            $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $appid);
            $cache->save($cacheKey, $newInfo, $expire_time);

            $authorizer_appid = $newInfo['authorizer_appid'];
            $cacheKey2 = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $authorizer_appid);
            $cache->save($cacheKey2, $newInfo, $expire_time);

            return $newInfo;
        } else {
            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
        }
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getExpireTime(array &$info)
    {
        $expire_time = 60;
        return $expire_time;
    }

    private function refreshInfo($cacheKey, $token)
    {
        $cache = $this->getDI()->get('cache');

        if (isset($token['access_token_expire']) && !empty($token['is_advanced'])) {
            if (strtotime($token['access_token_expire']) <= time()) {
                if (!empty($token['appid']) && !empty($token['secret']) && !empty($token['component_access_token']) && !empty($token['refresh_token']) && !empty($token['authorizer_appid'])) {
                    $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (!$objLock->lock()) {
                        $objToken = new \Weixin\Component($token['appid'], $token['secret']);
                        $objToken->setAccessToken($token['component_access_token']);
                        $arrToken = $objToken->apiAuthorizerToken($token['authorizer_appid'], $token['refresh_token']);
                        // $arrToken['authorizer_access_token'], $arrToken['authorizer_refresh_token'], $arrToken['expires_in']

                        if (!isset($arrToken['authorizer_access_token'])) {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($arrToken));
                        }

                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'access_token' => $arrToken['authorizer_access_token'],
                                'refresh_token' => $arrToken['authorizer_refresh_token'],
                                'access_token_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + intval($arrToken['expires_in']))
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
                        }
                    }
                }
            }

            // 缓存有效期不能超过token过期时间
            if ((time() + $this->_expire) > strtotime($token['access_token_expire'])) {
                $this->_expire = strtotime($token['access_token_expire']) - time();
            }
        }

        jsnoLock:
        // 获取jsapi_ticket
        if (!empty($token['is_advanced'])) {
            if (!isset($token['jsapi_ticket_expire']) || strtotime($token['jsapi_ticket_expire']) <= time()) {
                if (!empty($token['appid']) && !empty($token['secret']) && !empty($token['access_token'])) {
                    $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (!$objLock->lock()) {

                        // 获取jsapi_ticket
                        $objJssdk = new \Weixin\Jssdk();
                        $objJssdk->setAppId($token['appid']);
                        $objJssdk->setAppSecret($token['secret']);
                        $objJssdk->setAccessToken($token['access_token']);
                        $arrJsApiTicket = $objJssdk->getJsApiTicket();

                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'jsapi_ticket_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + $arrJsApiTicket['expires_in']),
                                'jsapi_ticket' => $arrJsApiTicket['ticket']
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
                        }
                    }
                }
            }
        }

        weixincardnoLock:
        // 获取微信卡券的api_ticket
        if (!empty($token['is_weixin_card'])) {
            if (!isset($token['wx_card_api_ticket_expire']) || strtotime($token['wx_card_api_ticket_expire']) <= time()) {
                if (!empty($token['appid']) && !empty($token['secret']) && !empty($token['access_token'])) {
                    $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (!$objLock->lock()) {

                        // 获取微信卡券的api_ticket
                        $weixin = new \Weixin\Client();
                        $weixin->setAccessToken($token['access_token']);
                        $ret = $weixin->getCardManager()->getApiTicket();

                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'wx_card_api_ticket_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + $ret['expires_in']),
                                'wx_card_api_ticket' => $ret['ticket']
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
                        }
                    }
                }
            }
        }
        return $token;
    }

    private function refreshComponentAccessTokenInfo($cacheKey, $token)
    {
        $cache = $this->getDI()->get('cache');
        if (isset($token['component_access_token_expire'])) {
            if (strtotime($token['component_access_token_expire']) <= time()) {
                if (!empty($token['appid']) && !empty($token['secret']) && !empty($token['component_verify_ticket'])) {
                    $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__);
                    $objLock = new \iLock($lockKey);
                    if (!$objLock->lock()) {
                        $objToken = new \Weixin\Component($token['appid'], $token['secret']);
                        $arrToken = $objToken->apiComponentToken($token['component_verify_ticket']);

                        if (!isset($arrToken['component_access_token'])) {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($arrToken));
                        }

                        $cmd = array();
                        $cmd['query'] = array(
                            '_id' => $token['_id']
                        );
                        $cmd['update'] = array(
                            '$set' => array(
                                'component_access_token' => $arrToken['component_access_token'],
                                'component_access_token_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + intval($arrToken['expires_in']))
                            )
                        );
                        $cmd['new'] = true;
                        $cmd['upsert'] = true;
                        $rst = $this->findAndModify($cmd);
                        if ($rst['ok'] == 1) {
                            $expire_time = $this->getExpireTime($rst['value']);
                            $cache->save($cacheKey, $rst['value'], $expire_time);
                            $objLock->release();
                            $token = $rst['value'];
                        } else {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($rst));
                        }
                    }
                }
            }

            // 缓存有效期不能超过token过期时间
            if ((time() + $this->_expire) > strtotime($token['component_access_token_expire'])) {
                $this->_expire = strtotime($token['component_access_token_expire']) - time();
            }
        }
        return $token;
    }
}
