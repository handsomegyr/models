<?php

namespace App\Common\Models\Banner\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Item extends Base
{

    /**
     * banner-item管理
     * This model is mapped to the table ibanner_item
     */
    public function getSource()
    {
        return 'ibanner_item';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['status'] = $this->changeToBoolean($data['status']);
        $data['start_at'] = $this->changeToMongoDate($data['start_at']);
        $data['end_at'] = $this->changeToMongoDate($data['end_at']);
        return $data;
    }
}
