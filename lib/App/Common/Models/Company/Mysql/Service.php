<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Service extends Base
{

    /**
     * 公司-服务管理
     * This model is mapped to the table icompany_service
     */
    public function getSource()
    {
        return 'icompany_service';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['params'] = $this->changeToArray($data['params']);
        return $data;
    }
}
