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

    public function updateJsapiTicket($id, $jsapi_ticket,  $expires_in, $memo = array())
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
            $cache->save($this->getCacheKey4AppId($newInfo['provider_appid'], $newInfo['authorizer_appid'], $newInfo['agentid']), $newInfo, $expire_time);
        }
        return $newInfo;
    }

    public function updateAgentInfo($id, $res, $now, $orginalMemo)
    {
        $updateData = array();
        $memo = array();
        $memo['get_agent_info_ret'] = $res;
        $updateData['memo'] = array_merge($orginalMemo, $memo);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "agentid": 1000005,
         * "name": "HR助手",
         * "square_logo_url": "https://p.qlogo.cn/bizmail/FicwmI50icF8GH9ib7rUAYR5kicLTgP265naVFQKnleqSlRhiaBx7QA9u7Q/0",
         * "description": "HR服务与员工自助平台",
         * "allow_userinfos": {
         * "user": [
         * {"userid": "zhangshan"},
         * {"userid": "lisi"}
         * ]
         * },
         * "allow_partys": {
         * "partyid": [1]
         * },
         * "allow_tags": {
         * "tagid": [1,2,3]
         * },
         * "close": 0,
         * "redirect_domain": "open.work.weixin.qq.com",
         * "report_location_flag": 0,
         * "isreportenter": 0,
         * "home_url": "https://open.work.weixin.qq.com"
         * }
         */
        $updateData['name'] = $res['name'];
        $updateData['square_logo_url'] = $res['square_logo_url'];
        $updateData['description'] = $res['description'];
        $updateData['allow_userinfos'] = !empty($res['allow_userinfos']) ? \App\Common\Utils\Helper::myJsonEncode($res['allow_userinfos']) : "{}";
        $updateData['allow_partys'] =  !empty($res['allow_partys']) ? \App\Common\Utils\Helper::myJsonEncode($res['allow_partys']) : "{}";
        $updateData['allow_tags'] =  !empty($res['allow_tags']) ? \App\Common\Utils\Helper::myJsonEncode($res['allow_tags']) : "{}";
        $updateData['close'] = $res['close'];
        $updateData['redirect_domain'] = $res['redirect_domain'];
        $updateData['report_location_flag'] = $res['report_location_flag'];
        $updateData['isreportenter'] = $res['isreportenter'];
        $updateData['home_url'] = $res['home_url'];
        $updateData['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        $affectRows = $this->update(array('_id' => $id), array('$set' => $updateData));
        return $affectRows;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4AppId($provider_appid, $authorizer_appid, $agentid)
    {
        $cacheKey = "agent:provider_appid:{$provider_appid}:authorizer_appid:{$authorizer_appid}:agentid:{$agentid}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        return $cacheKey;
    }

    private function refreshInfo($token)
    {
        $cache = $this->getDI()->get('cache');
        // 把昨天的key删除掉
        $yesterday = date("Ymd", time() - 24 * 3600);
        $cache->delete($this->getCacheKey4Appid($token['provider_appid'], $token['authorizer_appid'], $token['agentid']) . ":" . $yesterday);

        $ymd = date("Ymd");
        $requestLimitKey = $this->getCacheKey4Appid($token['provider_appid'], $token['authorizer_appid'], $token['agentid']) . ":" . $ymd;
        $requestTimes = $cache->get($requestLimitKey, 0);
        if (1500 < $requestTimes) {
            throw new \Exception("请求次数快要超限制2000");
        }

        if (empty($token['access_token_expire']) || strtotime($token['access_token_expire']) <= time()) {
            if (!empty($token['authorizer_appid']) && !empty($token['agentid'])) {
                $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['provider_appid'], $token['authorizer_appid'], $token['agentid']);
                $objLock = new \iLock($lockKey);
                if (!$objLock->lock()) {
                    $objToken = new \Qyweixin\Token\Server($token['authorizer_appid'], $token['secret']);
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

        // 
        jsnoLock:
        // 获取jsapi_ticket
        if (empty($token['jsapi_ticket_expire']) || strtotime($token['jsapi_ticket_expire']) <= time()) {
            if (!empty($token['access_token'])) {
                $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['provider_appid'], $token['authorizer_appid'], $token['agentid'], 'jsapi_ticket');
                $objLock = new \iLock($lockKey);
                if (!$objLock->lock()) {
                    // 获取jsapi_ticket
                    $objJssdk = new \Qyweixin\Jssdk();
                    // 企业内部应用的时候
                    if (empty($token['agent_type'])) {
                        // 获取企业的jsapi_ticket
                        $arrJsApiTicket = $objJssdk->getJsApiTicket($token['access_token']);
                    } else {
                        // 获取应用的jsapi_ticket
                        $arrJsApiTicket = $objJssdk->getJsApiTicket4Agent($token['access_token']);
                    }
                    $token = $this->updateJsapiTicket($token['id'], $arrJsApiTicket['ticket'], $arrJsApiTicket['expires_in']);
                }
            }
        }
        return $token;
    }

    protected $_expire = 0;
}
