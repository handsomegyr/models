<?php

namespace App\Common\Models\Cronjob\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Inspire extends Base
{

    /**
     * 计划任务-运行情况
     * This model is mapped to the table icronjob_inspire
     */
    public function getSource()
    {
        return 'icronjob_inspire';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}
