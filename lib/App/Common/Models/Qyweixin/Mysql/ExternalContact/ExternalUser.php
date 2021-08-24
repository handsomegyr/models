<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ExternalUser extends Base
{
    /**
     * 企业微信-外部联系人管理-客户
     * This model is mapped to the table iqyweixin_externalcontact_external_user
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_external_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['external_profile'] = $this->changeToArray($data['external_profile']);
        $data['follow_user'] = $this->changeToArray($data['follow_user']);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);

        return $data;
    }
}
