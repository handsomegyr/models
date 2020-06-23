<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ContactWay extends Base
{
    /**
     * 企业微信-外部联系人管理-客户联系联系我方式
     * This model is mapped to the table iqyweixin_externalcontact_contact_way
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_contact_way';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['skip_verify'] = $this->changeToBoolean($data['skip_verify']);
        $data['is_temp'] = $this->changeToBoolean($data['is_temp']);

        $data['conclusions_image_media_created_at'] = $this->changeToMongoDate($data['conclusions_image_media_created_at']);
        $data['conclusions_miniprogram_pic_media_created_at'] = $this->changeToMongoDate($data['conclusions_miniprogram_pic_media_created_at']);
        $data['sync_time'] = $this->changeToMongoDate($data['sync_time']);

        return $data;
    }
}
