<?php

namespace App\Common\Models\Qyweixin\Mysql\Contact;

use App\Common\Models\Base\Mysql\Base;

class BatchJob extends Base
{
    /**
     * 企业微信-通讯录管理-异步批量任务
     * This model is mapped to the table iqyweixin_batch_job
     */
    public function getSource()
    {
        return 'iqyweixin_batch_job';
    }
}
