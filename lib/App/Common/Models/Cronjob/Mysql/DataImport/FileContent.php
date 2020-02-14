<?php

namespace App\Common\Models\Cronjob\Mysql\DataImport;

use App\Common\Models\Base\Mysql\Base;

class FileContent extends Base
{

    /**
     * 计划任务-数据文件内容导入
     * This model is mapped to the table icronjob_data_import_file_content
     */
    public function getSource()
    {
        return 'icronjob_data_import_file_content';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['cron_time'] = $this->changeToMongoDate($data['cron_time']);
        $data['content'] = $this->changeToArray($data['content']);
        // $data['process_status'] = $this->changeToBoolean($data['process_status']);
        $data['process_time'] = $this->changeToMongoDate($data['process_time']);
        // $data['returnback_status'] = $this->changeToBoolean($data['returnback_status']);
        $data['returnback_time'] = $this->changeToMongoDate($data['returnback_time']);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        return $data;
    }
}
