<?php

namespace App\Weixin2\Models\Component;

use Cache;
use App\Services\Lock\MemcacheLock;

class Component extends \App\Common\Models\Weixin2\Component\Component
{

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppId($appid, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4Appid($appid);
        if ($is_get_latest || !Cache::tags($this->cache_tag)->has($cacheKey)) {
            $application = $this->getModel()
                ->where('appid', $appid)
                ->first();
            $application = $this->getReturnData($application);
            if (!empty($application)) {
                // 加缓存处理
                $expire_time = 5 * 60;
                Cache::tags($this->cache_tag)->put($cacheKey, $application, $expire_time);
            }
        } else {
            $application = Cache::tags($this->cache_tag)->get($cacheKey);
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
        $token = $this->getInfoByAppId($appid, true);
        if ($token == null) {
            return null;
        }

        // try {
        // $token = $this->refreshAccessTokenInfo($token);
        // } catch (\Exception $e) {
        // }

        return $token;
    }

    public function updateAccessToken($id, $access_token, $expires_in, $verify_ticket, $memo = array())
    {
        $updateData = array();
        $updateData['access_token'] = $access_token;
        $updateData['access_token_expire'] = date("Y-m-d H:i:s", time() + $expires_in);
        $updateData['verify_ticket'] = $verify_ticket;
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->updateById($id, $updateData);
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            Cache::tags($this->cache_tag)->put($this->getCacheKey4Appid($newInfo['appid']), $newInfo, $expire_time);
        }
        return $newInfo;
    }

    public function updateJsapiTicket($id, $jsapi_ticket, $expires_in, $memo = array())
    {
        $updateData = array();
        $updateData['jsapi_ticket'] = $jsapi_ticket;
        $updateData['jsapi_ticket_expire'] = date("Y-m-d H:i:s", time() + $expires_in);
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->updateById($id, $updateData);
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            Cache::tags($this->cache_tag)->put($this->getCacheKey4Appid($newInfo['appid']), $newInfo, $expire_time);
        }

        return $newInfo;
    }

    public function updateWxcardApiTicket($id, $wx_card_api_ticket, $expires_in, $memo = array())
    {
        $updateData = array();
        $updateData['wx_card_api_ticket'] = $wx_card_api_ticket;
        $updateData['wx_card_api_ticket_expire'] = date("Y-m-d H:i:s", time() + $expires_in);
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->updateById($id, $updateData);
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            Cache::tags($this->cache_tag)->put($this->getCacheKey4Appid($newInfo['appid']), $newInfo, $expire_time);
        }

        return $newInfo;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4Appid($appid)
    {
        $cacheKey = "component:component_appid:{$appid}";
        return $cacheKey;
    }

    private function refreshAccessTokenInfo($token)
    {
        if (isset($token['access_token_expire'])) {
            if (strtotime($token['access_token_expire']) <= time()) {
                if (!empty($token['appid']) && !empty($token['appsecret']) && !empty($token['verify_ticket'])) {
                    $lockKey = $this->cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__, $token['appid']);
                    $objLock = new MemcacheLock($lockKey, false);
                    if (!$objLock->lock()) {
                        $objToken = new \Weixin\Component($token['appid'], $token['appsecret']);
                        $arrToken = $objToken->apiComponentToken($token['verify_ticket']);

                        if (!isset($arrToken['access_token'])) {
                            throw new \Exception(\json_encode($arrToken));
                        }

                        $token = $this->updateAccessToken($id, $arrToken['access_token'], $arrToken['expires_in'], $token['verify_ticket']);
                    }
                }
            }

            // 缓存有效期不能超过token过期时间
            if ((time() + $this->_expire) > strtotime($token['access_token_expire'])) {
                $this->_expire = strtotime($token['access_token_expire']) - time();
            }
        }
        return $token;
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