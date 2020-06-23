<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class FollowUser extends Base
{
    /**
     * 企业微信-外部联系人管理-配置了客户联系功能的成员
     * This model is mapped to the table iqyweixin_externalcontact_follow_user
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_follow_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['get_time'] = $this->changeToMongoDate($data['get_time']);
        return $data;
    }
}
