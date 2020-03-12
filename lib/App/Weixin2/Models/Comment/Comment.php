<?php

namespace App\Weixin2\Models\Comment;

class Comment extends \App\Common\Models\Weixin2\Comment\Comment
{

    public function open($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_open'] = 1;
        $updateData['open_time'] = getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function close($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_open'] = 0;
        $updateData['close_time'] = getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
