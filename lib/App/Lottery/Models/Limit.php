<?php

namespace App\Lottery\Models;

class Limit extends \App\Common\Models\Lottery\Limit
{

    private $_limits = null;

    /**
     * 获取全部限定条件
     *
     * @param string $activity_id            
     * @param number $now            
     */
    public function getLimits($activity_id, $now)
    {
        if ($this->_limits == null) {
            $now = \App\Common\Utils\Helper::getCurrentTime($now);
            $this->_limits = $this->findAll(array(
                'activity_id' => $activity_id,
                'start_time' => array(
                    '$lte' => $now
                ),
                'end_time' => array(
                    '$gt' => $now
                )
            ));
        }
        return $this->_limits;
    }
}
