<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class UserBehaviorDataByUserid extends Base
{
    /**
     * 企业微信-外部联系人管理-联系客户按用户别统计数据
     * This model is mapped to the table iqyweixin_externalcontact_user_behavior_data_byuserid
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_user_behavior_data_byuserid';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['stat_time'] = $this->changeToValidDate($data['stat_time']);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        return $data;
    }
}
