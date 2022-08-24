<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class CorpTagGroup extends Base
{
    /**
     * 企业微信-外部联系人管理-企业标签组
     * This model is mapped to the table iqyweixin_externalcontact_corp_tag_group
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_corp_tag_group';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['tag_group_deleted'] = $this->changeToBoolean($data['tag_group_deleted']);
        $data['tag_group_create_time'] = $this->changeToValidDate($data['tag_group_create_time']);
        $data['get_time'] = $this->changeToValidDate($data['get_time']);
        return $data;
    }
}
