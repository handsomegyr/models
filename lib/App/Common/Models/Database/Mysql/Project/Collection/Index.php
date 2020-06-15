<?php

namespace App\Common\Models\Database\Mysql\Project\Collection;

use App\Common\Models\Base\Mysql\Base;

class Index extends Base
{

    /**
     * 数据库-数据库表索引管理
     * This model is mapped to the table idatabase_project_collection_index
     */
    public function getSource()
    {
        return 'idatabase_project_collection_index';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['indexes'] = $this->changeToArray($data['indexes']);
        $data['options'] = $this->changeToArray($data['options']);
        return $data;
    }
}
