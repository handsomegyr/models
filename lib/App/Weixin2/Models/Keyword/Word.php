<?php

namespace App\Weixin2\Models\Keyword;

class Word extends \App\Common\Models\Weixin2\Keyword\Word
{

    public function record($msg, $authorizer_appid, $component_appid, $agentid)
    {
        $cacheKey = "word:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}:agentid:{$agentid}:content:{$msg}";
        $cacheKey = cacheKey(__FILE__, __CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $id = $cache->get($cacheKey);
        if (empty($id)) {
            $info = $this->findOne(array(
                'content' => $msg,
                'agentid' => intval($agentid),
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
            $data['agentid'] = intval($agentid);
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['times'] = 1;
            $this->insert($data);
        }
    }
}
