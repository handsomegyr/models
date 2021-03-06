<?php

namespace App\Weixin2\Models\Comment;

class ReplyLog extends \App\Common\Models\Weixin2\Comment\ReplyLog
{

    public function recordCreateStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 1;
        $updateData['reply_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeCreateStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 0;
        $updateData['delete_reply_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    /**
     * 根据数据的唯一key获取信息
     *
     * @param string $msg_data_id            
     * @param string $index            
     * @param string $openid            
     * @param string $authorizer_appid            
     * @param string $component_appid           
     */
    public function getInfoByKey($msg_data_id, $index, $user_comment_id, $openid, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'msg_data_id' => $msg_data_id,
            'index' => $index,
            'user_comment_id' => $user_comment_id,
            'openid' => $openid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncReplyList($authorizer_appid, $component_appid, $msg_data_id, $index, $res, $now)
    {
        if (!empty($res['comment'])) {
            foreach ($res['comment'] as $comment) {

                $user_comment_id = $comment['user_comment_id'];
                $openid = $comment['openid'];

                if (!empty($comment['reply'])) {
                    foreach ($comment['reply'] as $item) {
                        $info = $this->getInfoByKey($msg_data_id, $index, $user_comment_id, $openid, $authorizer_appid, $component_appid);
                        $data = array();
                        /**
                         * “user_comment_id” : USER_COMMENT_ID //用户评论id
                         * “openid “: OPENID //openid
                         * “create_time “: CREATE_TIME //评论时间
                         * “content” : CONTENT //评论内容
                         * “comment_type “: IS_ELECTED //是否精选评论，0为即非精选，1为true，即精选
                         * “reply “: {
                         * “content “: CONTENT //作者回复内容
                         * “create_time” : CREATE_TIME //作者回复时间
                         * }
                         * }
                         */
                        $data['is_created'] = 1;
                        $data['content'] = $item['content'];
                        $data['reply_time'] = \App\Common\Utils\Helper::getCurrentTime($item['create_time']);

                        if (!empty($info)) {
                            $this->update(array('_id' => $info['_id']), array('$set' => $data));
                        } else {
                            $data['authorizer_appid'] = $authorizer_appid;
                            $data['component_appid'] = $component_appid;
                            $data['msg_data_id'] = $msg_data_id;
                            $data['index'] = $index;
                            $data['user_comment_id'] = $user_comment_id;
                            $data['openid'] = $openid;
                            $this->insert($data);
                        }
                    }
                }
            }
        }
    }
}
