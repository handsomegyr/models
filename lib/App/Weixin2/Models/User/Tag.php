<?php

namespace App\Weixin2\Models\User;

class Tag extends \App\Common\Models\Weixin2\User\Tag
{

    /**
     * 根据标签名获取信息
     *
     * @param string $name            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByName($name, $authorizer_appid, $component_appid)
    {
        $info = $this->getModel()
            ->where('name', $name)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function recordTagId($id, $res, $now)
    {
        $updateData = array();
        $updateData['tag_id'] = $res['tag']['id'];
        $updateData['tag_time'] = date("Y-m-d H:i:s", $now);
        $updateData['tag_count'] = empty($res['tag']['count']) ? 0 : $res['tag']['count'];
        return $this->updateById($id, $updateData);
    }

    public function removeTagId($id)
    {
        $updateData = array();
        $updateData['tag_id'] = "";
        return $this->updateById($id, $updateData);
    }

    public function syncTagList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['tags'])) {
            foreach ($res['tags'] as $tag) {
                $info = $this->getInfoByName($tag['name'], $authorizer_appid, $component_appid);
                $data = array();
                $data['tag_id'] = $tag['id'];
                $data['tag_time'] = date("Y-m-d H:i:s", $now);
                $data['tag_count'] = empty($tag['count']) ? 0 : $tag['count'];
                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['name'] = $tag['name'];
                    $this->insert($data);
                }
            }
        }
    }
}
