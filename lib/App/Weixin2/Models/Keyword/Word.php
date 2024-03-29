<?php

namespace App\Weixin2\Models\Keyword;

class Word extends \App\Common\Models\Weixin2\Keyword\Word
{

    public function record($msg, $authorizer_appid, $component_appid)
    {
        $cacheKey = "word:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}:content:{$msg}";
        $cacheKey = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $id = $cache->get($cacheKey);
        if (empty($id)) {
            $info = $this->findOne(array(
                'content' => $msg,
                'authorizer_appid' => $authorizer_appid,
                'component_appid' => $component_appid,
            ));
            if (!empty($info)) {
                $id = $info['_id'];
                // 加缓存处理
                $expire_time = 60 * 60; // 1小时
                $cache->save($cacheKey, $id, $expire_time);
            }
        }

        if (!empty($id)) {
            $updateData = array();
            $updateData['times'] = 1;
            $affectRows =  $this->update(array('_id' => $id), array('$inc' => $updateData));
        } else {
            $data = array();
            $data['content'] = $msg;
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['times'] = 1;
            $this->insert($data);
        }
    }
}
