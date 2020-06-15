<?php

namespace App\Common\Models\Database\Mysql\Plugin\Collection;

use App\Common\Models\Base\Mysql\Base;

class Data extends Base
{

    /**
     * 数据库-插件数据源管理
     * This model is mapped to the table idatabase_plugin_data
     */
    public function getSource()
    {
        return 'idatabase_plugin_collection_data';
    }
}
