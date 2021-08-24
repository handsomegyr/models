<?php

namespace App\Common\Models\Exchange\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Exchange extends Base
{

    /**
     * 兑换-兑换信息
     * This model is mapped to the table iexchange_exchange
     */
    public function getSource()
    {
        return 'iexchange_exchange';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_valid'] = $this->changeToBoolean($data['is_valid']);
        $data['got_time'] = $this->changeToValidDate($data['got_time']);

        return $data;
    }
}
