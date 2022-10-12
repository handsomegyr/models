<?php

namespace App\Qyweixin\Models\Authorize;

class Authorizer extends \App\Common\Models\Qyweixin\Authorize\Authorizer
{

    //应用类型 1:企业号
    const APPTYPE_QY = 1;

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppid($provider_appid, $appid, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4Appid($provider_appid, $appid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);

        if ($is_get_latest || empty($application)) {
            $application = $this->findOne(array(
                'appid' => $appid,
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
    public function getTokenByAppid($provider_appid, $appid)
    {
        $token = $this->getInfoByAppid($provider_appid, $appid, true);
        if ($token == null) {
            return null;
        }
        try {
            $token = $this->refreshInfo($token);
        } catch (\Exception $e) {
        }

        return $token;
    }

    public function createAndUpdateAuthorizer($provider_appid, $appid, $access_token, $refresh_token, $expires_in, $permanent_code, $extInfo, $memo = array())
    {
        $lockKey = $this->getCacheKey4Appid($provider_appid, $appid);
        $objLock = new \iLock($lockKey);
        if (!$objLock->lock()) {
            $token = $this->getInfoByAppid($provider_appid, $appid);
            if (empty($token)) {
                // 创建
                $datas = array(
                    'provider_appid' => $provider_appid,
                    'appid' => $appid,
                    'access_token' => $access_token,
                    'access_token_expire' => \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in),
                    'refresh_token' => $refresh_token,
                    'permanent_code' => $permanent_code,
                    'memo' => $memo
                );
                if (!empty($extInfo['dealer_corp_info'])) {
                    $datas['dealer_corp_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['dealer_corp_info']);
                }
                if (!empty($extInfo['auth_corp_info'])) {
                    if (!empty($extInfo['auth_corp_info']['corpid'])) {
                        $datas['auth_corpid'] = $extInfo['auth_corp_info']['corpid'];
                    }
                    $datas['auth_corp_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['auth_corp_info']);
                }
                if (!empty($extInfo['auth_info'])) {
                    $datas['auth_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['auth_info']);
                }
                if (!empty($extInfo['auth_user_info'])) {
                    $datas['auth_user_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['auth_user_info']);
                }
                if (!empty($extInfo['register_code_info'])) {
                    $datas['register_code_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['register_code_info']);
                }
                if (!empty($extInfo['admin'])) {
                    $datas['admin_list'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['admin']);
                }
                return $this->insert($datas);
            } else {
                $memo = array_merge($token['memo'], $memo);
                return $this->updateAccessToken($token['_id'], $access_token, $refresh_token, $expires_in, $permanent_code, $extInfo, $memo);
            }
        }
    }

    public function updateAccessToken($id, $access_token, $refresh_token, $expires_in, $permanent_code, $extInfo, $memo = array())
    {
        $updateData = array();
        if (!empty($access_token)) {
            $updateData['access_token'] = $access_token;
        }
        if (!empty($refresh_token)) {
            $updateData['refresh_token'] = $refresh_token;
        }
        if (!empty($permanent_code)) {
            $updateData['permanent_code'] = $permanent_code;
        }
        $updateData['access_token_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800);
        if (!empty($extInfo)) {
            if (!empty($extInfo['dealer_corp_info'])) {
                $updateData['dealer_corp_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['dealer_corp_info']);
            }
            if (!empty($extInfo['auth_corp_info'])) {
                if (!empty($extInfo['auth_corp_info']['corpid'])) {
                    $updateData['auth_corpid'] = $extInfo['auth_corp_info']['corpid'];
                }
                $updateData['auth_corp_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['auth_corp_info']);
            }
            if (!empty($extInfo['auth_info'])) {
                $updateData['auth_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['auth_info']);
            }
            if (!empty($extInfo['auth_user_info'])) {
                $updateData['auth_user_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['auth_user_info']);
            }
            if (!empty($extInfo['register_code_info'])) {
                $updateData['register_code_info'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['register_code_info']);
            }
            if (!empty($extInfo['admin'])) {
                $updateData['admin_list'] = \App\Common\Utils\Helper::myJsonEncode($extInfo['admin']);
            }
        }
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            $cache = $this->getDI()->get('cache');
            $cache->save($this->getCacheKey4Appid($newInfo['provider_appid'], $newInfo['appid']), $newInfo, $expire_time);
        }
        return $newInfo;
    }

    public function updateJsapiTicket($id, $jsapi_ticket, $expires_in, $memo = array())
    {
        $updateData = array();
        $updateData['jsapi_ticket'] = $jsapi_ticket;
        $updateData['jsapi_ticket_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800);
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            $cache = $this->getDI()->get('cache');
            $cache->save($this->getCacheKey4Appid($newInfo['provider_appid'], $newInfo['appid']), $newInfo, $expire_time);
        }

        return $newInfo;
    }

    public function updateWxcardApiTicket($id, $wx_card_api_ticket, $expires_in, $memo = array())
    {
        $updateData = array();
        $updateData['wx_card_api_ticket'] = $wx_card_api_ticket;
        $updateData['wx_card_api_ticket_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800);
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            $cache = $this->getDI()->get('cache');
            $cache->save($this->getCacheKey4Appid($newInfo['provider_appid'], $newInfo['appid']), $newInfo, $expire_time);
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
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        return $affectRows;
    }

    public function updateSuiteAccessToken($id, $suite_access_token, $expires_in, $suite_ticket, $memo = array())
    {
        $updateData = array();
        $updateData['suite_access_token'] = $suite_access_token;
        $updateData['suite_access_token_expire'] = \App\Common\Utils\Helper::getCurrentTime(time() + $expires_in - 1800);
        $updateData['suite_ticket'] = $suite_ticket;
        if (!empty($memo)) {
            $updateData["memo"] = $memo;
        }
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        // 重新获取数据
        $newInfo = $this->getInfoById($id);
        if (!empty($newInfo)) {
            $expire_time = 5 * 60;
            $cache = $this->getDI()->get('cache');
            $cache->save($this->getCacheKey4Appid($newInfo['provider_appid'], $newInfo['appid']), $newInfo, $expire_time);
        }
        return $newInfo;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4Appid($provider_appid, $appid)
    {
        $cacheKey = "authorizer:provider_appid:{$provider_appid}:appid:{$appid}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        return $cacheKey;
    }

    private function refreshInfo($token)
    {
        if (empty($token['access_token_expire']) || strtotime($token['access_token_expire']) <= time()) {
            if (!empty($token['appid'])) {
                $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['provider_appid'], $token['appid']);
                $objLock = new \iLock($lockKey);
                if (!$objLock->lock()) {
                    // 第3方应用的时候
                    if (!empty($token['provider_appid'])) {
                        if (!empty($token['suite_access_token']) && !empty($token['permanent_code']) && !empty($token['auth_corpid'])) {
                            $objToken = new \Qyweixin\Service();
                            $arrToken = $objToken->getCorpToken($token['suite_access_token'], $token['auth_corpid'], $token['permanent_code']);
                            $token = $this->updateAccessToken($token['_id'], $arrToken['access_token'], $arrToken['access_token'], $arrToken['expires_in'], "", null);
                        }
                    } else {
                        $objToken = new \Qyweixin\Token\Server($token['appid'], $token['appsecret']);
                        $arrToken = $objToken->getAccessToken();
                        if (!isset($arrToken['access_token'])) {
                            throw new \Exception(\App\Common\Utils\Helper::myJsonEncode($arrToken));
                        }
                        $token = $this->updateAccessToken($token['_id'], $arrToken['access_token'], $arrToken['access_token'], $arrToken['expires_in'], "", null);
                    }
                }
            }
        }

        // 缓存有效期不能超过token过期时间
        if ((time() + $this->_expire) > strtotime($token['access_token_expire'])) {
            $this->_expire = strtotime($token['access_token_expire']) - time();
        }

        // jsnoLock:
        // // 获取jsapi_ticket
        // if (empty($token['jsapi_ticket_expire']) || strtotime($token['jsapi_ticket_expire']) <= time()) {
        //     if (!empty($token['access_token'])) {
        //         $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['appid']);
        //         $objLock = new \iLock($lockKey);
        //         if (!$objLock->lock()) {
        //             // 获取jsapi_ticket
        //             $objJssdk = new \Weixin\Jssdk();
        //             $objJssdk->setAppId($token['appid']);
        //             $objJssdk->setAccessToken($token['access_token']);
        //             $arrJsApiTicket = $objJssdk->getJsApiTicket();
        //             $token = $this->updateJsapiTicket($token['_id'], $arrJsApiTicket['ticket'], $arrJsApiTicket['expires_in']);
        //         }
        //     }
        // }

        // weixincardnoLock:
        // // 获取微信卡券的api_ticket
        // if (!empty($token['is_weixin_card'])) {
        //     if (empty($token['wx_card_api_ticket_expire']) || strtotime($token['wx_card_api_ticket_expire']) <= time()) {
        //         if (!empty($token['access_token'])) {
        //             $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['appid']);
        //             $objLock = new \iLock($lockKey);
        //             if (!$objLock->lock()) {
        //                 // 获取微信卡券的api_ticket
        //                 $weixin = new \Weixin\Client();
        //                 $weixin->setAccessToken($token['access_token']);
        //                 $ret = $weixin->getCardManager()->getApiTicket();
        //                 $token = $this->updateWxcardApiTicket($token['_id'], $ret['ticket'], $ret['expires_in']);
        //             }
        //         }
        //     }
        // }
        return $token;
    }

    protected $_expire = 0;
}
