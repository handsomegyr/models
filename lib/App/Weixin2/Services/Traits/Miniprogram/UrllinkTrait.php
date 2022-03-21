<?php

namespace App\Weixin2\Services\Traits\Miniprogram;

trait UrllinkTrait
{
    // 创建小程序URL链接
    public function createMiniappUrlLink($urllink_id)
    {
        $modelUrllink = new \App\Weixin2\Models\Miniprogram\Urllink();
        $urllinkInfo = $modelUrllink->getInfoById($urllink_id);
        if (empty($urllinkInfo)) {
            throw new \Exception("小程序URL链接记录ID:{$urllink_id}所对应的记录不存在");
        }

        $path = trim($urllinkInfo['path']);
        $query = trim($urllinkInfo['query_content']);
        // 将%改成()，前台会把()再次改成%
        $query = str_ireplace('%', '()', $query);
        $is_expire = intval($urllinkInfo['is_expire']) ? true : false;
        $expire_type = intval($urllinkInfo['expire_type']);
        $expire_time = strtotime($urllinkInfo['expire_time']);
        $expire_interval = intval($urllinkInfo['expire_interval']);

        $cloud_base = new \Weixin\Wx\Model\CloudBase();
        $urllinkManager = $this->getWeixinObject()->getWxClient()->getUrllinkManager();
        $res = $urllinkManager->generate($path, $query, $is_expire, $expire_type, $expire_time, $expire_interval, $cloud_base);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelUrllink->recordUrllink($urllink_id, $res['url_link']);
        return $res['url_link'];
    }
}
