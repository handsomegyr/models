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
        $data['last_upload_time'] = $this->changeToValidDate($data['last_upload_time']);
        // $data['enabled'] = $this->changeToBoolean($data['enabled']);
        $data['isSystem'] = $this->changeToBoolean($data['isSystem']);
        // $data['online'] = $this->changeToBoolean($data['online']);
        $data['ae'] = $this->changeToArray($data['ae']);
        $data['executives'] = $this->changeToArray($data['executives']);
        $data['components'] = $this->changeToArray($data['components']);
        return $data;
    }
}
