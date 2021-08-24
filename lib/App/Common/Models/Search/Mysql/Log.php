<?php

namespace App\Common\Models\Search\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 搜索-搜索日志管理
     * This model is mapped to the table isearch_log
     */
    public function getSource()
    {
        return 'isearch_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToValidDate($data['log_time']);
        return $data;
    }
}
