<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class MomentTask extends Base
{
    /**
     * 企业微信-外部联系人管理-企业发表内容到客户的朋友圈
     * This model is mapped to the table iqyweixin_externalcontact_moment_task
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_moment_task';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['job_create_time'] = $this->changeToValidDate($data['job_create_time']);
        $data['job_result_time'] = $this->changeToValidDate($data['job_result_time']);
        return $data;
    }
}
