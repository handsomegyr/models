<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class CorpTag extends Base
{
    /**
     * 企业微信-外部联系人管理-企业标签
     * This model is mapped to the table iqyweixin_externalcontact_corp_tag
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_corp_tag';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['tag_deleted'] = $this->changeToBoolean($data['tag_deleted']);
        $data['tag_group_deleted'] = $this->changeToBoolean($data['tag_group_deleted']);

        $data['tag_create_time'] = $this->changeToMongoDate($data['tag_create_time']);
        $data['tag_group_create_time'] = $this->changeToMongoDate($data['tag_group_create_time']);
        $data['get_time'] = $this->changeToMongoDate($data['get_time']);
        return $data;
    }
}
