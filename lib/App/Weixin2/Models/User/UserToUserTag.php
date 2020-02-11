<?php

namespace App\Weixin2\Models\User;

class UserToUserTag extends \App\Common\Models\Weixin2\User\UserToUserTag
{

    public function getOpenidListByTagId($tag_id, $authorizer_appid, $component_appid)
    {
        $q = $this->getModel()->query();
        $q->where('tag_id', $tag_id)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid);
        $list = $q->get();
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $item = $this->getReturnData($item);
                $ret[] = $item['openid'];
            }
        }
        return $ret;
    }

    public function tag($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_tag'] = 1;
        $updateData['tag_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }

    public function untag($id, $now)
    {
        $updateData = array();
        $updateData['is_tag'] = 0;
        $updateData['untag_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }
}
