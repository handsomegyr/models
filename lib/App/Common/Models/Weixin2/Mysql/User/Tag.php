<?php

namespace App\Common\Models\Weixin2\Mysql\User;

use App\Common\Models\Base\Mysql\Base;

class Tag extends Base
{
    /**
     * 微信-用户标签
     * This model is mapped to the table iweixin2_user_tag
     */
    public function getSource()
    {
        return 'iweixin2_user_tag';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['tag_time'] = $this->changeToMongoDate($data['tag_time']);
        return $data;
    }
}
