<?php

namespace App\Weixin2\Models\Miniprogram\SubscribeMsg;

class Msg extends \App\Common\Models\Weixin2\Miniprogram\SubscribeMsg\Msg
{
    public function getListByIdsAndTemplateMsgType($ids, $authorizer_appid, $component_appid)
    {
        $ret = $this->findAll(array(
            '_id' => array('$in' => $ids),
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ), array('priority' => -1, '_id' => -1));
        return $ret;
    }
}
