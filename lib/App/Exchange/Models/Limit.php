<?php

namespace App\Exchange\Models;

class Limit extends \App\Common\Models\Exchange\Limit
{
    private $_limits = null;

    /**
     * 获取全部限定条件
     *
     * @param string $activity_id            
     * @param number $now            
     * @param string $prize_id            
     */
    public function getLimits($activity_id, $now, $prize_id)
    {
        if ($this->_limits == null) {
            $now = getCurrentTime($now);
            $query = array(
                'activity_id' => $activity_id,
                'start_time' => array(
                    '$lte' => $now
                ),
                'end_time' => array(
                    '$gt' => $now
                ),
                'prize_id' => $prize_id
            );
            $this->_limits = $this->findAll($query, array(
                '_id' => -1
            ));
        }
        return $this->_limits;
    }
}
