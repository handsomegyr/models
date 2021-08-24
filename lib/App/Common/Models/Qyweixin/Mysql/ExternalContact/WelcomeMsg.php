<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class WelcomeMsg extends Base
{
    /**
     * 企业微信-外部联系人管理-新客户欢迎语
     * This model is mapped to the table iqyweixin_externalcontact_welcome_msg
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_welcome_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['image_media_created_at'] = $this->changeToValidDate($data['image_media_created_at']);
        $data['miniprogram_pic_media_created_at'] = $this->changeToValidDate($data['miniprogram_pic_media_created_at']);
        $data['send_time'] = $this->changeToValidDate($data['send_time']);
        return $data;
    }
}
