<?php

namespace App\Weixin2\Services\Traits;

trait ShorturlTrait
{
    public function shorturl($shorturl_id)
    {
        $modelShorturl = new \App\Weixin2\Models\Shorturl();
        $shorturlInfo = $modelShorturl->getInfoById($shorturl_id);
        if (empty($shorturlInfo)) {
            throw new \Exception("短连接记录ID:{$shorturl_id}所对应的记录不存在");
        }
        $action = $shorturlInfo['action'];
        $res = $this->getWeixinObject()
            ->getShortUrlManager()
            ->$action($shorturlInfo['long_url']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelShorturl->updateCreatedStatus($shorturl_id, $res, time());
        return $res;
    }
}
