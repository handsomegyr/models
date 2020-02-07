<?php

namespace App\Common\Models\System\Mysql;

use App\Common\Models\Base\Mysql\Base;

class OperationLog extends Base
{

    /**
     * 操作日志
     * This model is mapped to the table errorlog
     */
    public function getSource()
    {
        return 'operationlog';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['happen_time'] = $this->changeToMongoDate($data['happen_time']);
        $data['params'] = $this->changeToArray($data['params']);
        return $data;
    }
}
