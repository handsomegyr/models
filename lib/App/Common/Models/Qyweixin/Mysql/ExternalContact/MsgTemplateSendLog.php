<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class MsgTemplateSendLog extends Base
{
    /**
     * 企业微信-外部联系人管理-企业群发消息发送日志
     * This model is mapped to the table iqyweixin_externalcontact_msg_template_send_log
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_msg_template_send_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['check_status'] = $this->changeToBoolean($data['check_status']);
        $data['image_media_created_at'] = $this->changeToValidDate($data['image_media_created_at']);
        $data['miniprogram_pic_media_created_at'] = $this->changeToValidDate($data['miniprogram_pic_media_created_at']);
        $data['send_time'] = $this->changeToValidDate($data['send_time']);
        $data['get_result_time'] = $this->changeToValidDate($data['get_result_time']);

        $data['fail_list'] = $this->changeToArray($data['fail_list']);
        $data['detail_list'] = $this->changeToArray($data['detail_list']);
        return $data;
    }
}
