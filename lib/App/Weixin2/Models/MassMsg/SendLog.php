<?php

namespace App\Weixin2\Models\MassMsg;

class SendLog extends \App\Common\Models\Weixin2\MassMsg\SendLog
{

    /**
     * 记录
     */
    public function record($component_appid, $authorizer_appid, $mass_msg_id, $mass_msg_name, $msg_type, $media, $media_id, $thumb_media, $thumb_media_id, $title, $description, $card_id, $card_ext, $upload_media_id, $upload_media_created_at, $upload_media_type, $is_to_all, $tag_id, $touser, $send_ignore_reprint, $clientmsgid, $keyword_id, $keyword, $keyword_mass_msg_type, $ToUserName, $FromUserName, $mass_msg_content, $msg_id, $msg_data_id, $log_time)
    {
        $datas = array(
            'component_appid' => $component_appid,
            'authorizer_appid' => $authorizer_appid,
            'mass_msg_id' => empty($mass_msg_id) ? 0 : $mass_msg_id,
            'mass_msg_name' => empty($mass_msg_name) ? "" : $mass_msg_name,
            'msg_type' => empty($msg_type) ? "" : $msg_type,
            'media' => empty($media) ? 0 : $media,
            'media_id' => empty($media_id) ? "" : $media_id,
            'thumb_media' => empty($thumb_media) ? 0 : $thumb_media,
            'thumb_media_id' => empty($thumb_media_id) ? "" : $thumb_media_id,
            'title' => empty($title) ? "" : $title,
            'description' => empty($description) ? "" : $description,
            'card_id' => empty($card_id) ? "" : $card_id,
            'card_ext' => empty($card_ext) ? "" : $card_ext,
            'upload_media_id' => empty($upload_media_id) ? "" : $upload_media_id,
            'upload_media_created_at' => empty($upload_media_created_at) ? "" : $upload_media_created_at,
            'upload_media_type' => empty($upload_media_type) ? "" : $upload_media_type,
            'is_to_all' => empty($is_to_all) ? 0 : 1,
            'tag_id' => empty($tag_id) ? 0 : $tag_id,
            'touser' => empty($touser) ? "" : $touser,
            'send_ignore_reprint' => empty($send_ignore_reprint) ? 0 : 1,
            'clientmsgid' => empty($clientmsgid) ? "" : $clientmsgid,
            'keyword_id' => empty($keyword_id) ? 0 : $keyword_id,
            'keyword' => empty($keyword) ? "" : $keyword,
            'keyword_mass_msg_type' => empty($keyword_mass_msg_type) ? "" : $keyword_mass_msg_type,
            'ToUserName' => empty($ToUserName) ? "" : $ToUserName,
            'FromUserName' => empty($FromUserName) ? "" : $FromUserName,
            'mass_msg_content' => empty($mass_msg_content) ? "" : $mass_msg_content,
            'msg_id' => empty($msg_id) ? "" : $msg_id,
            'msg_data_id' => empty($msg_data_id) ? "" : $msg_data_id,
            'msg_time' => getCurrentTime($log_time),
            'log_time' => getCurrentTime($log_time),
            'msg_status' => ""
        );

        return $this->insert($datas);
    }

    public function recordUploadResult($id, $res, $now)
    {
        $updateData = array();
        $updateData['upload_media_id'] = $res['media_id'];
        $updateData['upload_media_created_at'] = $res['created_at'];
        $updateData['upload_media_type'] = $res['type'];
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMsgId($id, $res, $now)
    {
        $updateData = array();
        $updateData['msg_id'] = $res['msg_id'];
        if (!empty($res['msg_data_id'])) {
            $updateData['msg_data_id'] = $res['msg_data_id'];
        }
        $updateData['msg_time'] = getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordMsgStatus($id, $res)
    {
        $updateData = array();
        $updateData['msg_status'] = $res['msg_status'];
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeMsgId($id)
    {
        $updateData = array();
        $updateData['msg_id'] = "";
        $updateData['msg_data_id'] = "";
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
