<?php

namespace App\Weixin2\Models\Miniprogram;

class Urlscheme extends \App\Common\Models\Weixin2\Miniprogram\Urlscheme
{
    public function recordOpenlink($id, $openlink)
    {
        $updateData = array();
        $updateData['openlink'] = $openlink;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
