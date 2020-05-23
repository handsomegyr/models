<?php

namespace App\Common\Models\Database\Mysql\Project;

use App\Common\Models\Base\Mysql\Base;

class Plugin extends Base
{

    /**
     * 数据库-参数设置表管理
     * This model is mapped to the table idatabase_project_plugin
     */
    public function getSource()
    {
        return 'idatabase_project_plugin';
    }
}
