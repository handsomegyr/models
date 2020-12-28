<?php

namespace App\Common\Models\Exchange\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Rule extends Base
{

    /**
     * 兑换-规则
     * This model is mapped to the table iexchange_rule
     */
    public function getSource()
    {
        return 'iexchange_rule';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['allow_start_time'] = $this->changeToMongoDate($data['allow_start_time']);
        $data['allow_end_time'] = $this->changeToMongoDate($data['allow_end_time']);
        return $data;
    }
}
