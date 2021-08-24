<?php

namespace App\Common\Models\Weixin2\Mysql\DataCube;

use App\Common\Models\Base\Mysql\Base;

class UserReadHour extends Base
{

    /**
     * 微信-图文统计分时数据
     * This model is mapped to the table iweixin2_datacube_userreadhour
     */
    public function getSource()
    {
        return 'iweixin2_datacube_userreadhour';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['ref_date'] = $this->changeToValidDate($data['ref_date']);
        return $data;
    }
}
