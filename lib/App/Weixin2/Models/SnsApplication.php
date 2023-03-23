<?php

namespace App\Weixin2\Models;

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
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if ($is_get_latest || empty($application)) {
            $application = $this->findOne(array(
                'appid' => $appid
            ));

            if (!empty($application)) {
                // 加缓存处理
                $expire_time = 13 * 60;
                $cache->save($cacheKey, $application, $expire_time);
            }
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

    public function checkIsIpValid($appConfig, $ip)
    {
        // 是否开启
        if (empty($appConfig['is_ip_check'])) {
            return true;
        }

        if (empty($appConfig['ip_list'])) {
            return false;
        } else {
            $ip_list = $appConfig['ip_list'];
            if (!is_array($ip_list)) {
                $ip_list = \json_decode($ip_list, true);
            }
            if (empty($ip_list)) {
                return false;
            }
            if (in_array(trim($ip), $ip_list)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function checkIsCbUrlValid($appConfig, $url)
    {
        // 是否开启
        if (empty($appConfig['is_cb_url_check'])) {
            return true;
        }

        if (empty($appConfig['cb_url_list'])) {
            return false;
        } else {
            $cb_url_list = $appConfig['cb_url_list'];
            if (!is_array($cb_url_list)) {
                $cb_url_list = \json_decode($cb_url_list, true);
            }
            if (empty($cb_url_list)) {
                return false;
            }
            if (in_array(trim($url), $cb_url_list)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getCacheKey4Appid($appid)
    {
        $cacheKey = "sns_application:appid:{$appid}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        return $cacheKey;
    }
}
