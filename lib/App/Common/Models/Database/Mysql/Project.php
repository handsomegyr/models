<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Project extends Base
{

    /**
     * 数据库-数据库管理
     * This model is mapped to the table idatabase_project
     */
    public function getSource()
    {
        return 'idatabase_project';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['isSystem'] = $this->changeToBoolean($data['isSystem']);
        return $data;
    }
}
