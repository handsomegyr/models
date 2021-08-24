<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class Moment extends Base
{
    /**
     * 企业微信-外部联系人管理-客户朋友圈
     * This model is mapped to the table iqyweixin_externalcontact_moment
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_moment';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['create_time'] = $this->changeToValidDate($data['create_time']);
        return $data;
    }
}
