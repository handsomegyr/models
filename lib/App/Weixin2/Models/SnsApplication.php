<?php

namespace App\Weixin2\Models;

use Cache;

class SnsApplication extends \App\Common\Models\Weixin2\SnsApplication
{

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppid($appid, $is_get_latest = false)
    {
        $cacheKey = $this->getCacheKey4Appid($appid);
        if ($is_get_latest || !Cache::tags($this->cache_tag)->has($cacheKey)) {
            $application = $this->getModel()
                ->where('appid', $appid)
                ->first();
            $application = $this->getReturnData($application);
            if (!empty($application)) {
                // 加缓存处理
                $expire_time = 13 * 60;
                Cache::tags($this->cache_tag)->put($cacheKey, $application, $expire_time);
            }
        } else {
            $application = Cache::tags($this->cache_tag)->get($cacheKey);
        }
        return $application;
    }

    public function checkIsValid($appConfig, $now)
    {
        // 是否开启
        if (empty($appConfig['is_active'])) {
            return false;
        }
        // 开始时间未到
        if (strtotime($appConfig['start_time']) > $now) {
            return false;
        }
        // 结束时间已到
        if (strtotime($appConfig['end_time']) < $now) {
            return false;
        }
        return true;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4Appid($appid)
    {
        $cacheKey = "sns_application:appid:{$appid}";
        return $cacheKey;
    }
}
