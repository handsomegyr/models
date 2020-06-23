<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupChatStatisticByUserid extends Base
{
    /**
     * 企业微信-外部联系人管理-客户群按群主别统计数据
     * This model is mapped to the table iqyweixin_externalcontact_group_chat_statistic_byuserid
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_chat_statistic_byuserid';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['order_asc'] = $this->changeToBoolean($data['order_asc']);
        $data['day_begin_time'] = $this->changeToMongoDate($data['day_begin_time']);
        $data['get_time'] = $this->changeToMongoDate($data['get_time']);
        return $data;
    }
}
