<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ExternalUserRemark extends Base
{
    /**
     * 企业微信-外部联系人管理-修改客户备注信息
     * This model is mapped to the table iqyweixin_externalcontact_external_user_remark
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_external_user_remark';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['remark_mobiles'] = $this->changeToArray($data['remark_mobiles']);
        $data['remark_pic_media_created_at'] = $this->changeToMongoDate($data['remark_pic_media_created_at']);
        $data['update_remark_time'] = $this->changeToMongoDate($data['update_remark_time']);

        return $data;
    }
}
