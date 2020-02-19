<?php

namespace App\Exchange\Models;

class Record extends \App\Common\Models\Exchange\Record
{

    public function record($activity_id, $user_id, $source, $result_id, $result_msg, $rule_id, $exchange_id)
    {
        $datas = array(
            'activity_id' => $activity_id,
            'user_id' => $user_id,
            'source' => $source,
            'result_id' => $result_id,
            'result_msg' => $result_msg,
            'rule_id' => $rule_id,
            'exchange_id' => $exchange_id
        );
        return $this->insert($datas);
    }

    public function getTotal($activity_id, $user_id, $success = false)
    {
        if ($success == true) {
            return $this->count(array(
                'activity_id' => $activity_id,
                'user_id' => $user_id,
                'result_id' => 1
            ));
        } else {
            return $this->count(array(
                'activity_id' => $activity_id,
                'user_id' => $user_id
            ));
        }
    }
}
