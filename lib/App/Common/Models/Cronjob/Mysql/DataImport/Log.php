<?php

namespace App\Common\Models\Cronjob\Mysql\DataImport;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 计划任务-数据导入日志
     * This model is mapped to the table icronjob_data_import_log
     */
    public function getSource()
    {
        return 'icronjob_data_import_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
