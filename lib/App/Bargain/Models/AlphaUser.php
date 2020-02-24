<?php

namespace App\Bargain\Models;

class AlphaUser extends \App\Common\Models\Bargain\AlphaUser
{

    /**
     * 根据user_id获取信息
     *
     * @param string $user_id  
     * @param string $activity_id           
     * @return array
     */
    public function getInfoByUserId($user_id, $activity_id)
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => $activity_id
        );
        $info = $this->findOne($query);
        return $info;
    }
}
