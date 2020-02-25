<?php

namespace App\Common\Models\Game\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 游戏-日志
     * This model is mapped to the table igame_log
     */
    public function getSource()
    {
        return 'igame_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['play_time'] = $this->changeToMongoDate($data['play_time']);
        return $data;
    }
}
