<?php
namespace App\Common\Models\Live\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Auchor extends Base
{

    /**
     * 直播-主播管理
     * This model is mapped to the table ilive_auchor
     */
    public function getSource()
    {
        return 'ilive_auchor';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_vip'] = $this->changeToBoolean($data['is_vip']);
        return $data;
    }
}