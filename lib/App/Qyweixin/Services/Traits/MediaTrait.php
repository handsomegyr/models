<?php

namespace App\Qyweixin\Services\Traits;

trait MediaTrait
{
    /**
     * 所有文件size必须大于5个字节
     * 图片（image）：2MB，支持JPG,PNG格式
     * 语音（voice） ：2MB，播放长度不超过60s，仅支持AMR格式
     * 视频（video） ：10MB，支持MP4格式
     * 普通文件（file）：20MB
     */
    public function uploadMedia($mediaInfo)
    {
        $modelMedia = new \App\Qyweixin\Models\Media\Media();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($mediaInfo)) {
            $media_rec_id = $mediaInfo;
            $mediaInfo = $modelMedia->getInfoById($media_rec_id);
            if (empty($mediaInfo)) {
                throw new \Exception("临时素材记录ID:{$media_rec_id}所对应的临时素材不存在");
            }
        }

        $filePath = $modelMedia->getPhysicalFilePath($mediaInfo['media']);
        $res = $this->uploadMediaByApi($filePath, $mediaInfo['type'], $mediaInfo['media_id'], $mediaInfo['media_time']);

        // 发生了改变就更新
        if ($res['media_id'] != $mediaInfo['media_id']) {
            $modelMedia->recordMediaId($mediaInfo['id'], $res, time());
        }

        return $res;
    }

    // 检查媒体文件是否过期
    public function isMediaTimeExpired($media_id, $media_time)
    {
        // 媒体文件在微信后台保存时间为3天，即3天后media_id失效。
        $expire_seconds = 24 * 3600 * 2.7;

        // 如果已上传并且没有过期
        if (!empty($media_id) && (strtotime($media_time) + $expire_seconds) > time()) {
            // throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材是已上传并且没有过期");
            return false;
        } else {
            return true;
        }
    }

    /**
     * 所有文件size必须大于5个字节
     * 图片（image）：2MB，支持JPG,PNG格式
     * 语音（voice） ：2MB，播放长度不超过60s，仅支持AMR格式
     * 视频（video） ：10MB，支持MP4格式
     * 普通文件（file）：20MB
     */
    public function uploadMediaByApi($media_url, $type, $media_id, $media_time)
    {
        // 检查是否过期
        $isExpired = $this->isMediaTimeExpired($media_id, $media_time);

        // 如果没有过期
        if (!$isExpired) {
            // throw new \Exception("临时素材记录ID:{$media_id}所对应的临时素材是已上传并且没有过期");
            $res = array();
            $res['media_id'] = $media_id;
            return $res;
        }

        $res = $this->getQyWeixinObject()
            ->getMediaManager()
            ->upload($type, $media_url);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [type] => thumb
        // [created_at] => 1557741369
        return $res;
    }

    /**
     * 上传图片
     */
    public function uploadMediaImgByApi($media_url)
    {
        $res = $this->getQyWeixinObject()
            ->getMediaManager()
            ->uploadImg($media_url);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [url] => http://p.qpic.cn/pic_wework/1696231148/61675075f03f76a4c20d8a547a3418d646ec2e2de106ff9a/0
        return $res;
    }
}
