<?php

namespace App\Common\Models\Weixin2\Mysql\Comment;

use App\Common\Models\Base\Mysql\Base;

class ReplyLog extends Base
{

    /**
     * 微信-已群发文章评论回复日志
     * This model is mapped to the table iweixin2_comment_reply_log
     */
    public function getSource()
    {
        return 'iweixin2_comment_reply_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['reply_time'] = $this->changeToValidDate($data['reply_time']);
        $data['delete_reply_time'] = $this->changeToValidDate($data['delete_reply_time']);

        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
