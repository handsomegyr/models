<?php

namespace App\Weixin2\Models\Keyword;

use DB;
use Cache;

class Word extends \App\Common\Models\Weixin2\Keyword\Word
{

    public function record($msg, $authorizer_appid, $component_appid)
    {
        $id = 0;
        $cacheKey = "word:authorizer_appid:{$authorizer_appid}:component_appid:{$component_appid}:content:{$msg}";
        if (!Cache::tags($this->cache_tag)->has($cacheKey)) {
            $info = $this->getModel()
                ->where("authorizer_appid", $authorizer_appid)
                ->where("component_appid", $component_appid)
                ->where("content", $msg)
                ->first();
            if (!empty($info)) {
                $id = $info->id;
                // 加缓存处理
                $expire_time = 60 * 60; // 1小时
                Cache::tags($this->cache_tag)->put($cacheKey, $id, $expire_time);
            }
        } else {
            $id = Cache::tags($this->cache_tag)->get($cacheKey);
        }

        if (!empty($id)) {
            $updateData = array();
            $updateData['times'] = DB::raw("times+1");
            $affectRows = $this->updateById($id, $updateData);
        } else {
            $data = array();
            $data['authorizer_appid'] = $authorizer_appid;
            $data['component_appid'] = $component_appid;
            $data['content'] = $msg;
            $data['times'] = 1;
            $this->insert($data);
        }
    }
}
