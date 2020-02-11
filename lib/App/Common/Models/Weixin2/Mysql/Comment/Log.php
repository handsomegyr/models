<?php

namespace App\Common\Models\Weixin2\Mysql\Comment;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{
    /**
     * 微信-已群发文章评论日志
     * This model is mapped to the table iweixin2_comment_log
     */
    public function getSource()
    {
        return 'iweixin2_comment_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['comment_time'] = $this->changeToMongoDate($data['comment_time']);
        $data['delete_comment_time'] = $this->changeToMongoDate($data['delete_comment_time']);

        $data['comment_type'] = $this->changeToBoolean($data['comment_type']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        return $data;
    }
}
