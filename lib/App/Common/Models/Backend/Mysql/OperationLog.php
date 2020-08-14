<?php

namespace App\Common\Models\Backend\Mysql;

use App\Common\Models\Base\Mysql\Base;

class OperationLog extends Base
{

    /**
     * 操作日志
     * This model is mapped to the table ibackend_operationlog
     */
    public function getSource()
    {
        return 'ibackend_operationlog';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['happen_time'] = $this->changeToMongoDate($data['happen_time']);
        $data['params'] = $this->changeToArray($data['params']);
        return $data;
    }
}
