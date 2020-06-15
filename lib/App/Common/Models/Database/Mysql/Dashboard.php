<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Dashboard extends Base
{

    /**
     * 数据库-仪表盘表管理
     * This model is mapped to the table idatabase_dashboard
     */
    public function getSource()
    {
        return 'idatabase_dashboard';
    }
}
