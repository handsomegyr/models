<?php

namespace App\Common\Models\Game\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * 有些-玩家
     * This model is mapped to the table igame_user
     */
    public function getSource()
    {
        return 'igame_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['max_score_time'] = $this->changeToValidDate($data['max_score_time']);

        return $data;
    }
}
