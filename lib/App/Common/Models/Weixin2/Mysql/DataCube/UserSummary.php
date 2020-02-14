<?php

namespace App\Common\Models\Weixin2\Mysql\DataCube;

use App\Common\Models\Base\Mysql\Base;

class UserSummary extends Base
{

    /**
     * 微信-用户增减数据
     * This model is mapped to the table iweixin2_datacube_usersummary
     */
    public function getSource()
    {
        return 'iweixin2_datacube_usersummary';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['ref_date'] = $this->changeToMongoDate($data['ref_date']);
        return $data;
    }
}