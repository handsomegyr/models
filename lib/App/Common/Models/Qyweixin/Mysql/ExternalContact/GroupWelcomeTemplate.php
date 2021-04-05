<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class GroupWelcomeTemplate extends Base
{
    /**
     * 企业微信-外部联系人管理-群欢迎语素材
     * This model is mapped to the table iqyweixin_externalcontact_group_welcome_template
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_group_welcome_template';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['image_media_created_at'] = $this->changeToMongoDate($data['image_media_created_at']);
        $data['miniprogram_pic_media_created_at'] = $this->changeToMongoDate($data['miniprogram_pic_media_created_at']);
        $data['sync_time'] = $this->changeToMongoDate($data['sync_time']);
        $data['is_notify'] = $this->changeToBoolean($data['is_notify']);
        return $data;
    }
}
