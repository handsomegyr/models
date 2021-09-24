<?php

namespace App\Qyweixin\Models\Keyword;

class Word extends \App\Common\Models\Qyweixin\Keyword\Word
{

    public function record($msg, $authorizer_appid, $provider_appid, $agentid)
    {
        $cacheKey = "word:authorizer_appid:{$authorizer_appid}:provider_appid:{$provider_appid}:agentid:{$agentid}:content:{$msg}";
        $cacheKey = cacheKey(__CLASS__, $cacheKey);
        $cache = $this->getDI()->get('cache');
        $id = $cache->get($cacheKey);
        if (empty($id)) {
            $info = $this->findOne(array(
                'content' => $msg,
                'agentid' => intval($agentid),
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid,
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
            $data['provider_appid'] = $provider_appid;
            $data['times'] = 1;
            $this->insert($data);
        }
    }
}
