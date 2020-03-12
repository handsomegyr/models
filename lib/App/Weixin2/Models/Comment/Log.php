<?php

namespace App\Weixin2\Models\Comment;

class Log extends \App\Common\Models\Weixin2\Comment\Log
{

    public function markelect($id, $res, $now)
    {
        $updateData = array();
        $updateData['comment_type'] = 1;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function unmarkelect($id, $res, $now)
    {
        $updateData = array();
        $updateData['comment_type'] = 0;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function recordCreateStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 1;
        $updateData['comment_time'] = getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeCreateStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 0;
        $updateData['delete_comment_time'] = getCurrentTime($now);
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

    public function syncCommentList($authorizer_appid, $component_appid, $msg_data_id, $index, $res, $now)
    {
        if (!empty($res['comment'])) {
            foreach ($res['comment'] as $item) {

                $user_comment_id = $item['user_comment_id'];
                $openid = $item['openid'];

                $info = $this->getInfoByKey($msg_data_id, $index, $user_comment_id, $openid, $authorizer_appid, $component_appid);
                $data = array();
                /**
                 * “user_comment_id” : USER_COMMENT_ID //用户评论id
                 * “openid “: OPENID //openid
                 * “create_time “: CREATE_TIME //评论时间
                 * “content” : CONTENT //评论内容
                 * “comment_type “: IS_ELECTED //是否精选评论，0为即非精选，1为true，即精选
                 */
                $data['comment_type'] = $item['comment_type'];
                $data['content'] = $item['content'];
                $data['comment_time'] = getCurrentTime($item['create_time']);
                $data['is_created'] = 1;
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
