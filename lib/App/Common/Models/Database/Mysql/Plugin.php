<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Plugin extends Base
{

    /**
     * 数据库-插件表管理
     * This model is mapped to the table idatabase_plugin
     */
    public function getSource()
    {
        return 'idatabase_plugin';
    }
}
