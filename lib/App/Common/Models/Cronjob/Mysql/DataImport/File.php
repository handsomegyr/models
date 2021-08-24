<?php

namespace App\Common\Models\Cronjob\Mysql\DataImport;

use App\Common\Models\Base\Mysql\Base;

class File extends Base
{

    /**
     * 计划任务-数据文件导入
     * This model is mapped to the table icronjob_data_import_file
     */
    public function getSource()
    {
        return 'icronjob_data_import_file';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['cron_time'] = $this->changeToValidDate($data['cron_time']);
        // $data['status'] = $this->changeToBoolean($data['status']);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
