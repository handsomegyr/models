<?php

namespace App\Weixin2\Models\Miniprogram;

class Urllink extends \App\Common\Models\Weixin2\Miniprogram\Urllink
{
    public function recordUrllink($id, $url_link)
    {
        $updateData = array();
        $updateData['url_link'] = $url_link;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
