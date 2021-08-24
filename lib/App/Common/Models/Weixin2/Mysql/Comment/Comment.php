<?php

namespace App\Common\Models\Weixin2\Mysql\Comment;

use App\Common\Models\Base\Mysql\Base;

class Comment extends Base
{
    /**
     * 微信-已群发文章评论
     * This model is mapped to the table iweixin2_comment
     */
    public function getSource()
    {
        return 'iweixin2_comment';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['open_time'] = $this->changeToValidDate($data['open_time']);
        $data['close_time'] = $this->changeToValidDate($data['close_time']);

        $data['is_open'] = $this->changeToBoolean($data['is_open']);
        return $data;
    }
}
