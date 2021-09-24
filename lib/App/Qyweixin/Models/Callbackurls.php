<?php

namespace App\Qyweixin\Models;

class Callbackurls extends \App\Common\Models\Qyweixin\Callbackurls
{
    public function getValidCallbackUrlList($authorizer_appid, $provider_appid, $agentid, $is_get_latest = false)
    {
        $cacheKey = "callbackurls:authorizer_appid:{$authorizer_appid}:provider_appid:{$provider_appid}:agentid:{$agentid}:callbackurllist";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $list = $cache->get($cacheKey);

        if ($is_get_latest || empty($list)) {
            $ret = $this->findAll(array(
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid,
                'agentid' => $agentid,
                'is_valid' => true
            ));
            $list = array();
            if (!empty($ret)) {
                foreach ($ret as $item) {
                    $list[] = $item['url'];
                }
            }
            if (!empty($list)) {
                // 加缓存处理
                $expire_time = 5 * 60;
                $cache->save($cacheKey, $list, $expire_time);
            }
        }
        return $list;
    }

    public function isValid($authorizer_appid, $provider_appid, $agentid, $url)
    {
        $callbackUrls = $this->getValidCallbackUrlList($authorizer_appid, $provider_appid, $agentid);
        if (empty($callbackUrls)) {
            return false;
        }
        $hostname = $this->getHost($url);
        if (in_array($hostname, $callbackUrls)) {
            return true;
        }
        $pos = strpos($hostname, '.');
        if ($pos === false) {
        } else {
            $hostname = substr($hostname, $pos + 1);
            if (in_array($hostname, $callbackUrls)) {
                return true;
            }
        }

        return false;
    }

    public function getHost($Address)
    {
        $parseUrl = parse_url(trim($Address));
        return trim(isset($parseUrl['host']) ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
    }
}
