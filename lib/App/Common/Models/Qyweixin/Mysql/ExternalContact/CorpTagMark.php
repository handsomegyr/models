<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class CorpTagMark extends Base
{
    /**
     * 企业微信-外部联系人管理-编辑客户企业标签
     * This model is mapped to the table iqyweixin_externalcontact_corp_tag_mark
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_corp_tag_mark';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['add_tag'] = $this->changeToArray($data['add_tag']);
        $data['remove_tag'] = $this->changeToArray($data['remove_tag']);
        $data['mark_tag_time'] = $this->changeToMongoDate($data['mark_tag_time']);

        return $data;
    }
}
