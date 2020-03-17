<?php

namespace App\Weixin2\Models\User;

class UserToUserTag extends \App\Common\Models\Weixin2\User\UserToUserTag
{

    public function getOpenidListByTagId($tag_id, $authorizer_appid, $component_appid)
    {
        $list = $this->findAll(array(
            'tag_id' => $tag_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['openid'];
            }
        }

        return $ret;
    }

    public function tag($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_tag'] = 1;
        $updateData['tag_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function untag($id, $now)
    {
        $updateData = array();
        $updateData['is_tag'] = 0;
        $updateData['untag_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }
}
