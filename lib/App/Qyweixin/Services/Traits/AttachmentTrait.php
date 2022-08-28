<?php

namespace App\Qyweixin\Services\Traits;

trait AttachmentTrait
{
    /**
     * 上传的附件资源限制
     * 所有文件size必须大于5个字节
     * 图片（image）：10MB，支持JPG,PNG格式，朋友圈类型图片不超过1440 x 1080
     * 视频（video） ：10MB，支持MP4格式，朋友圈类型视频时长不超过30秒
     * 文件（file） ：10MB
     * 注：目前 商品图册只支持图片类型； 朋友圈只支持图片、视频类型 
     */
    public function uploadAttachment($attachmentInfo)
    {
        $modelAttachment = new \App\Qyweixin\Models\Attachment\Attachment();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($attachmentInfo)) {
            $media_rec_id = $attachmentInfo;
            $attachmentInfo = $modelAttachment->getInfoById($media_rec_id);
            if (empty($attachmentInfo)) {
                throw new \Exception("附件资源记录ID:{$media_rec_id}所对应的附件资源不存在");
            }
        }

        $filePath = $modelAttachment->getPhysicalFilePath($attachmentInfo['media']);
        $res = $this->uploadAttachmentByApi($filePath, $attachmentInfo['media_type'], $attachmentInfo['attachment_type'], $attachmentInfo['media_id'], $attachmentInfo['media_time']);

        // 发生了改变就更新
        if ($res['media_id'] != $attachmentInfo['media_id']) {
            $modelAttachment->recordMediaId($attachmentInfo['id'], $res, time());
        }

        return $res;
    }

    // 检查附件资源是否过期
    public function isAttachmentTimeExpired($media_id, $media_time)
    {
        // 附件资源在微信后台保存时间为3天，即3天后media_id失效。
        $expire_seconds = 24 * 3600 * 2.7;

        // 如果已上传并且没有过期
        if (!empty($media_id) && (strtotime($media_time) + $expire_seconds) > time()) {
            // throw new \Exception("附件资源记录ID:{$media_id}所对应的附件资源是已上传并且没有过期");
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
    public function uploadAttachmentByApi($media_url, $media_type, $attachment_type, $media_id, $media_time)
    {
        // 检查是否过期
        $isExpired = $this->isAttachmentTimeExpired($media_id, $media_time);

        // 如果没有过期
        if (!$isExpired) {
            // throw new \Exception("附件资源记录ID:{$media_id}所对应的附件资源是已上传并且没有过期");
            $res = array();
            $res['media_id'] = $media_id;
            return $res;
        }

        $res = $this->getQyWeixinObject()
            ->getMediaManager()
            ->uploadAttachment($media_type, $attachment_type, $media_url);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // [type] => thumb
        // [created_at] => 1557741369
        return $res;
    }
}
