<?php

namespace App\Common\Models\Vote\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Subject extends Base
{

    /**
     * 投票-主题表管理
     * This model is mapped to the table ivote_subject
     */
    public function getSource()
    {
        return 'ivote_subject';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['is_closed'] = $this->changeToBoolean($data['is_closed']);
        return $data;
    }
}
