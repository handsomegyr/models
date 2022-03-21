<?php

namespace App\Weixin2\Services\Traits;

trait CommentTrait
{

    public function openComment($comment_id)
    {
        $modelComment = new \App\Weixin2\Models\Comment\Comment();
        $commentInfo = $modelComment->getInfoById($comment_id);
        if (empty($commentInfo)) {
            throw new \Exception("已群发文章评论记录ID:{$comment_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->open($commentInfo['user_comment_id'], $commentInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelComment->open($comment_id, $res, time());
        return $res;
    }

    public function closeComment($comment_id)
    {
        $modelComment = new \App\Weixin2\Models\Comment\Comment();
        $commentInfo = $modelComment->getInfoById($comment_id);
        if (empty($commentInfo)) {
            throw new \Exception("已群发文章评论记录ID:{$comment_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->close($commentInfo['user_comment_id'], $commentInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelComment->close($comment_id, $res, time());
        return $res;
    }

    public function syncCommentList($comment_id)
    {
        $modelComment = new \App\Weixin2\Models\Comment\Comment();
        $commentInfo = $modelComment->getInfoById($comment_id);
        if (empty($commentInfo)) {
            throw new \Exception("已群发文章评论记录ID:{$comment_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->getlist($commentInfo['user_comment_id'], $commentInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * “errcode”: 0,
         * “errmsg” : “ok”,
         * “total”: TOTAL //总数，非comment的size around
         * “comment”: [{
         * “user_comment_id” : USER_COMMENT_ID //用户评论id
         * “openid “: OPENID //openid
         * “create_time “: CREATE_TIME //评论时间
         * “content” : CONTENT //评论内容
         * “comment_type “: IS_ELECTED //是否精选评论，0为即非精选，1为true，即精选
         * “reply “: {
         * “content “: CONTENT //作者回复内容
         * “create_time” : CREATE_TIME //作者回复时间
         * }
         * }]
         * }
         */
        $now = time();
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $modelCommentLog->syncCommentList($commentInfo['authorizer_appid'], $commentInfo['component_appid'], $commentInfo['msg_data_id'], $commentInfo['index'], $res, $now);

        $modelCommentReplyLog = new \App\Weixin2\Models\Comment\ReplyLog();
        $modelCommentReplyLog->syncReplyList($commentInfo['authorizer_appid'], $commentInfo['component_appid'], $commentInfo['msg_data_id'], $commentInfo['index'], $res, $now);

        return $res;
    }

    public function markelectComment($comment_log_id)
    {
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $commentLogInfo = $modelCommentLog->getInfoById($comment_log_id);
        if (empty($commentLogInfo)) {
            throw new \Exception("已群发文章评论日志记录ID:{$comment_log_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->markelect($commentLogInfo['user_comment_id'], $commentLogInfo['msg_data_id'], $commentLogInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentLog->markelect($comment_log_id, $res, time());
        return $res;
    }

    public function unmarkelectComment($comment_log_id)
    {
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $commentLogInfo = $modelCommentLog->getInfoById($comment_log_id);
        if (empty($commentLogInfo)) {
            throw new \Exception("已群发文章评论日志记录ID:{$comment_log_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->unmarkelect($commentLogInfo['user_comment_id'], $commentLogInfo['msg_data_id'], $commentLogInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentLog->unmarkelect($comment_log_id, $res, time());
        return $res;
    }

    public function deleteCommentLog($comment_log_id)
    {
        $modelCommentLog = new \App\Weixin2\Models\Comment\Log();
        $commentLogInfo = $modelCommentLog->getInfoById($comment_log_id);
        if (empty($commentLogInfo)) {
            throw new \Exception("已群发文章评论日志记录ID:{$comment_log_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->delete($commentLogInfo['user_comment_id'], $commentLogInfo['msg_data_id'], $commentLogInfo['index']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentLog->removeCreateStatus($comment_log_id, $res, time());

        return $res;
    }

    public function addCommentReply($comment_reply_log_id)
    {
        $modelCommentReplyLog = new \App\Weixin2\Models\Comment\ReplyLog();
        $commentReplyLogInfo = $modelCommentReplyLog->getInfoById($comment_reply_log_id);
        if (empty($commentReplyLogInfo)) {
            throw new \Exception("已群发文章评论回复日志记录ID:{$comment_reply_log_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->replyAdd($commentReplyLogInfo['user_comment_id'], $commentReplyLogInfo['content'], $commentReplyLogInfo['msg_data_id'], $commentReplyLogInfo['index']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentReplyLog->recordCreateStatus($comment_reply_log_id, $res, time());
        return $res;
    }

    public function deleteCommentReply($comment_reply_log_id)
    {
        $modelCommentReplyLog = new \App\Weixin2\Models\Comment\ReplyLog();
        $commentReplyLogInfo = $modelCommentReplyLog->getInfoById($comment_reply_log_id);
        if (empty($commentReplyLogInfo)) {
            throw new \Exception("已群发文章评论回复日志记录ID:{$comment_reply_log_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getCommentManager()
            ->replyDelete($commentReplyLogInfo['user_comment_id'], $commentReplyLogInfo['msg_data_id'], $commentReplyLogInfo['index']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCommentReplyLog->removeCreateStatus($comment_reply_log_id, $res, time());

        return $res;
    }
}
