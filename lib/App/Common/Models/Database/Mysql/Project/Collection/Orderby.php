<?php

namespace App\Common\Models\Database\Mysql\Project\Collection;

use App\Common\Models\Base\Mysql\Base;

class Orderby extends Base
{

    /**
     * 数据库-数据库表显示排序管理
     * This model is mapped to the table idatabase_project_collection_orderby
     */
    public function getSource()
    {
        return 'idatabase_project_collection_orderby';
    }
}
