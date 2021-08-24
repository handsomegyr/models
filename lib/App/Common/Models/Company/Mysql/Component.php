<?php

namespace App\Common\Models\Company\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Component extends Base
{

    /**
     * 公司-组件管理
     * This model is mapped to the table icompany_component
     */
    public function getSource()
    {
        return 'icompany_component';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['publish_time'] = $this->changeToValidDate($data['publish_time']);
        $data['is_publish'] = $this->changeToBoolean($data['is_publish']);
        return $data;
    }
}
