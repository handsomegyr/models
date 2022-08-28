<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class MomentSendResult extends Base
{
    /**
     * 企业微信-外部联系人管理-客户朋友圈发表后的可见客户
     * This model is mapped to the table iqyweixin_externalcontact_moment_send_result
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_moment_send_result';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        return $data;
    }
}
