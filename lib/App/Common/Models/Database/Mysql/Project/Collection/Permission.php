<?php

namespace App\Common\Models\Database\Mysql\Project\Collection;

use App\Common\Models\Base\Mysql\Base;

class Permission extends Base
{

    /**
     * 数据库-数据库表结构管理
     * This model is mapped to the table idatabase_project_collection_permission
     */
    public function getSource()
    {
        return 'idatabase_project_collection_permission';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['collection_ids'] = $this->changeToArray($data['collection_ids']);
        return $data;
    }
}
