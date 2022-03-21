<?php

namespace App\Weixin2\Services\Traits;

trait MediaTrait
{
    public function uploadMedia($media_id)
    {
        $modelMedia = new \App\Weixin2\Models\Media\Media();
        $mediaInfo = $modelMedia->getInfoById($media_id);
        if (empty($mediaInfo)) {
            throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材不存在");
        }
        // 媒体文件在微信后台保存时间为3天，即3天后media_id失效。
        $expire_seconds = 24 * 3600 * 2.7;

        // 如果已上传并且没有过期
        if (!empty($mediaInfo['media_id']) && (strtotime($mediaInfo['media_time']) + $expire_seconds) > time()) {
            // throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材是已上传并且没有过期");
            $res['media_id'] = $mediaInfo['media_id'];
            return $res;
        }
        $filePath = $modelMedia->getPhysicalFilePath($mediaInfo['media']);

        $res = $this->getWeixinObject()
            ->getMediaManager()
            ->upload($mediaInfo['type'], $filePath);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [type] => thumb
        // [thumb_media_id] => RjJp_zlifrTf3RPmgSrXpRwdg9WXY31JGrhyz6mVWthYgmna2BgRqSeCnlGF47oY
        // [created_at] => 1557741369
        if (empty($res['media_id']) && !empty($res['thumb_media_id'])) {
            $res['media_id'] = $res['thumb_media_id'];
        }
        $modelMedia->recordMediaId($media_id, $res, time());
        return $res;
    }
}
