<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Project extends Base
{

    /**
     * 公司-项目管理
     * This model is mapped to the table icompany_project
     */
    public function getSource()
    {
        return 'icompany_project';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['last_upload_time'] = $this->changeToMongoDate($data['last_upload_time']);
        $data['enabled'] = $this->changeToBoolean($data['enabled']);
        $data['online'] = $this->changeToBoolean($data['online']);
        return $data;
    }
}
