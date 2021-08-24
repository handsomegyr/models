<?php

namespace App\Common\Models\Bargain\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Bargain extends Base
{

    /**
     * 砍价-砍价物规则管理
     * This model is mapped to the table ibargain_bargain
     */
    public function getSource()
    {
        return 'ibargain_bargain';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['launch_time'] = $this->changeToValidDate($data['launch_time']);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['is_closed'] = $this->changeToBoolean($data['is_closed']);
        $data['is_both_bargain'] = $this->changeToBoolean($data['is_both_bargain']);
        $data['is_bargain_to_minworth'] = $this->changeToBoolean($data['is_bargain_to_minworth']);
        $data['bargain_to_minworth_time'] = $this->changeToValidDate($data['bargain_to_minworth_time']);
        $data['bargain_time'] = $this->changeToValidDate($data['bargain_time']);
        $data['close_time'] = $this->changeToValidDate($data['close_time']);

        return $data;
    }
}
