<?php

namespace App\Common\Models\Game\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Game extends Base
{

    /**
     * 游戏-游戏
     * This model is mapped to the table igame_game
     */
    public function getSource()
    {
        return 'igame_game';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToValidDate($data['start_time']);
        $data['end_time'] = $this->changeToValidDate($data['end_time']);
        $data['max_score_time'] = $this->changeToValidDate($data['max_score_time']);
        return $data;
    }
}
