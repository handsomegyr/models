<?php

namespace App\Weixin2\Models\Authorize;

use Cache;
use App\Services\Lock\MemcacheLock;

class Authorizer extends \App\Common\Models\Weixin2\Authorize\Authorizer
{

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppid($component_appid, $appid, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4Appid($component_appid, $appid);
        if ($is_get_latest || !Cache::tags($this->cache_tag)->has($cacheKey)) {
            $application = $this->getModel()
                ->where('appid', $appid)
                ->where('component_appid', $component_appid)
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
    public function getTokenByAppid($component_appid, $appid)
    {
        $token = $this->getInfoByAppid($component_appid, $appid, true);
        if ($token == null) {
            return null;
        }
        try {
            $token = $this->refreshInfo($token);
        } catch (\Exception $e) {
        }

        return $token;
    }

    public function createAndUpdateAuthorizer($component_appid, $appid, $access_token, $refresh_token, $expires_in, $func_info, $memo = array())
    {
        $lockKey = $this->getCacheKey4Appid($component_appid, $appid);
        $objLock = new MemcacheLock($lockKey, false);
        if (!$objLock->lock()) {
            $token = $this->getInfoByAppid($component_appid, $appid);
            if (empty($token)) {
                // 创建
                $datas = array(
                    'component_appid' => $component_appid,
                    'appid' => $appid,
                    'access_token' => $access_token,
                    'access_token_expire' => date("Y-m-d H:i:s", time() + $expires_in),
                    'refresh_token' => $refresh_token,
                    'func_info' => \json_encode($func_info),
                    'memo' => $memo
                );
                return $this->insert($datas);
            } else {
                $memo = array_merge($token['memo'], $memo);
                return $this->updateAccessToken($token['id'], $access_token, $refresh_token, $expires_in, $func_info, $memo);
            }
        }
    }

    public function updateAccessToken($id, $access_token, $refresh_token, $expires_in, $func_info, $memo = array())
    {
        $updateData = array();
        $updateData['access_token'] = $access_token;
        $updateData['refresh_token'] = $refresh_token;
        $updateData['access_token_expire'] = date("Y-m-d H:i:s", time() + $expires_in);
        if (!empty($func_info)) {
            $updateData['func_info'] = \json_encode($func_info);
        }
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->updateById($id, $updateData);
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            Cache::tags($this->cache_tag)->put($this->getCacheKey4Appid($newInfo['component_appid'], $newInfo['appid']), $newInfo, $expire_time);
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
            Cache::tags($this->cache_tag)->put($this->getCacheKey4Appid($newInfo['component_appid'], $newInfo['appid']), $newInfo, $expire_time);
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
            Cache::tags($this->cache_tag)->put($this->getCacheKey4Appid($newInfo['component_appid'], $newInfo['appid']), $newInfo, $expire_time);
        }

        return $newInfo;
    }

    public function updateAuthorizerInfo($id, $res, $orginalMemo)
    {
        $updateData = array();
        $memo = array();
        $memo['get_authorizer_info_ret'] = $res;
        $updateData['memo'] = array_merge($orginalMemo, $memo);
        $updateData['nick_name'] = $res['authorizer_info']['nick_name'];
        $updateData['head_img'] = $res['authorizer_info']['head_img'];
        $updateData['user_name'] = $res['authorizer_info']['user_name'];
        $updateData['alias'] = $res['authorizer_info']['alias'];
        $updateData['qrcode_url'] = $res['authorizer_info']['qrcode_url'];
        // $updateData['principal_name'] = $res['authorizer_info']['principal_name'];
        // $updateData['signature'] = $res['authorizer_info']['signature'];
        $affectRows = $this->updateById($id, $updateData);
        return $affectRows;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4Appid($component_appid, $appid)
    {
        $cacheKey = "authorizer:component_appid:{$component_appid}:appid:{$appid}";
        return $cacheKey;
    }

    private function refreshInfo($token)
    {
        if (empty($token['access_token_expire']) || strtotime($token['access_token_expire']) <= time()) {
            if (!empty($token['component_appid']) && !empty($token['refresh_token']) && !empty($token['appid'])) {
                $lockKey = $this->cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__, $token['component_appid'], $token['appid']);
                $objLock = new MemcacheLock($lockKey, false);
                if (!$objLock->lock()) {
                    $modelComponent = new \App\Components\Weixinopen\Services\Models\Component\ComponentModel();
                    $componentInfo = $modelComponent->getInfoByAppId($token['component_appid'], true);
                    if (!empty($componentInfo)) {
                        $objToken = new \Weixin\Component($componentInfo['appid'], $componentInfo['appsecret']);
                        $objToken->setAccessToken($componentInfo['access_token']);
                        $arrToken = $objToken->apiAuthorizerToken($token['appid'], $token['refresh_token']);
                        $token = $this->updateAccessToken($token['id'], $arrToken['authorizer_access_token'], $arrToken['authorizer_refresh_token'], $arrToken['expires_in'], null);
                    }
                }
            }
        }

        // 缓存有效期不能超过token过期时间
        if ((time() + $this->_expire) > strtotime($token['access_token_expire'])) {
            $this->_expire = strtotime($token['access_token_expire']) - time();
        }

        jsnoLock:
        // 获取jsapi_ticket
        if (empty($token['jsapi_ticket_expire']) || strtotime($token['jsapi_ticket_expire']) <= time()) {
            if (!empty($token['access_token'])) {
                $lockKey = $this->cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__, $token['appid']);
                $objLock = new MemcacheLock($lockKey, false);
                if (!$objLock->lock()) {
                    // 获取jsapi_ticket
                    $objJssdk = new \Weixin\Jssdk();
                    $objJssdk->setAppId($token['appid']);
                    $objJssdk->setAccessToken($token['access_token']);
                    $arrJsApiTicket = $objJssdk->getJsApiTicket();
                    $token = $this->updateJsapiTicket($token['id'], $arrJsApiTicket['ticket'], $arrJsApiTicket['expires_in']);
                }
            }
        }

        weixincardnoLock:
        // 获取微信卡券的api_ticket
        if (!empty($token['is_weixin_card'])) {
            if (empty($token['wx_card_api_ticket_expire']) || strtotime($token['wx_card_api_ticket_expire']) <= time()) {
                if (!empty($token['access_token'])) {
                    $lockKey = $this->cacheKey(__FILE__, __CLASS__, __METHOD__, __LINE__, $token['appid']);
                    $objLock = new MemcacheLock($lockKey, false);
                    if (!$objLock->lock()) {
                        // 获取微信卡券的api_ticket
                        $weixin = new \Weixin\Client();
                        $weixin->setAccessToken($token['access_token']);
                        $ret = $weixin->getCardManager()->getApiTicket();
                        $token = $this->updateWxcardApiTicket($token['id'], $ret['ticket'], $ret['expires_in']);
                    }
                }
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