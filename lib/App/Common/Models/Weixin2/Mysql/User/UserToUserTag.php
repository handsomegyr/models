<?php

namespace App\Common\Models\Weixin2\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class UserToUserTag extends Base
{

    /**
     * 微信-授权事件接收日志
     * This model is mapped to the table iweixin2_user_to_usertag
     */
    public function getSource()
    {
        return 'iweixin2_user_to_usertag';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['tag_time'] = $this->changeToValidDate($data['tag_time']);
        $data['untag_time'] = $this->changeToValidDate($data['untag_time']);

        $data['is_tag'] = $this->changeToBoolean($data['is_tag']);
        return $data;
    }
}
