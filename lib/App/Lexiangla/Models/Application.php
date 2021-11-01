<?php

namespace App\Lexiangla\Models;

class Application extends \App\Common\Models\Lexiangla\Application
{
    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppid($AppKey, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4Appid($AppKey);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if ($is_get_latest || empty($application)) {
            $application = $this->findOne(array(
                'AppKey' => $AppKey
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
    public function getTokenByAppid($AppKey)
    {
        $token = $this->getInfoByAppid($AppKey, true);
        if ($token == null) {
            // throw new \Exception("xxxxxxxxxxxxxxx:{$provider_appid}:{$authorizer_appid}:{$AppKey}");
            return null;
        }
        try {
            $token = $this->refreshInfo($token);
        } catch (\Exception $e) {
        }

        return $token;
    }

    private function refreshInfo($token)
    {
        // // 把昨天的key删除掉
        // $yesterday = date("Ymd", time() - 24 * 3600);
        // Cache::tags($this->cache_tag)->delete($this->getCacheKey4Appid($token['AppKey']) . ":" . $yesterday);

        // $ymd = date("Ymd");
        // $requestLimitKey = $this->getCacheKey4Appid($token['AppKey']) . ":" . $ymd;
        // $requestTimes = Cache::tags($this->cache_tag)->get($requestLimitKey, 0);
        // if (1500 < $requestTimes) {
        //     throw new \Exception("请求次数快要超限制2000");
        // }

        if (empty($token['access_token_expire']) || strtotime($token['access_token_expire']) <= time()) {
            if (!empty($token['AppKey'])) {
                $lockKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, __METHOD__, __LINE__, $token['AppKey']);
                $objLock = new \iLock($lockKey);
                if (!$objLock->lock()) {
                    // 要加一个请求次数的限制
                    $objLxapi = new \Lexiangla\Openapi\Api($token['AppKey'], $token['AppSecret']);
                    $access_token = $objLxapi->getAccessToken();
                    // Cache::tags($this->cache_tag)->increment($requestLimitKey, 1);
                    if (empty($access_token)) {
                        throw new \Exception("未获取到AccessToken的值");
                    }
                    $token = $this->updateAccessToken($token['id'], $access_token, 7200);
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
            $cache->save($this->getCacheKey4AppId($newInfo['AppKey']), $newInfo, $expire_time);
        }
        return $newInfo;
    }

    private function getCacheKey4Appid($AppKey)
    {
        $cacheKey = "lexiangla:AppKey:{$AppKey}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        return $cacheKey;
    }
}
