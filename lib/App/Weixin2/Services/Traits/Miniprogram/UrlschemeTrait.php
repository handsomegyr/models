<?php

namespace App\Weixin2\Services\Traits\Miniprogram;

trait UrlschemeTrait
{
    // 创建小程序URL链接
    public function createMiniappUrlScheme($urlscheme_id)
    {
        $modelUrlscheme = new \App\Weixin2\Models\Miniprogram\Urlscheme();
        $urlschemeInfo = $modelUrlscheme->getInfoById($urlscheme_id);
        if (empty($urlschemeInfo)) {
            throw new \Exception("小程序URL链接记录ID:{$urlscheme_id}所对应的记录不存在");
        }

        $path = trim($urlschemeInfo['path']);
        $query = trim($urlschemeInfo['query_content']);
        // 将%改成()，前台会把()再次改成%
        $query = str_ireplace('%', '()', $query);
        $is_expire = intval($urlschemeInfo['is_expire']) ? true : false;
        $expire_type = intval($urlschemeInfo['expire_type']);
        $expire_time = strtotime($urlschemeInfo['expire_time']);
        $expire_interval = intval($urlschemeInfo['expire_interval']);

        $jumpwxa = new \Weixin\Wx\Model\JumpWxa();
        $jumpwxa->path = $path;
        $jumpwxa->query = $query;
        $urlschemeManager = $this->getWeixinObject()->getWxClient()->getUrlschemeManager();
        $res = $urlschemeManager->generate($jumpwxa, $is_expire, $expire_type, $expire_time, $expire_interval);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelUrlscheme->recordOpenlink($urlscheme_id, $res['openlink']);
        return $res['openlink'];
    }
}
