<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ExternalUserFollowUser extends Base
{
    /**
     * 企业微信-外部联系人管理-添加外部联系人的企业成员
     * This model is mapped to the table iqyweixin_externalcontact_external_user_follow_user
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_external_user_follow_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['tags'] = $this->changeToArray($data['tags']);
        $data['remark_mobiles'] = $this->changeToArray($data['remark_mobiles']);
        $data['createtime'] = $this->changeToValidDate($data['createtime']);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);

        return $data;
    }
}
