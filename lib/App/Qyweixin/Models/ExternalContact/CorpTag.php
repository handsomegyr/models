<?php

namespace App\Qyweixin\Models\ExternalContact;

class CorpTag extends \App\Common\Models\Qyweixin\ExternalContact\CorpTag
{

    /**
     * 根据tag_id获取信息
     *
     * @param string $tag_id            
     * @param string $authorizer_appid          
     */
    public function getInfoByCorpTag($tag_id, $authorizer_appid)
    {
        $query = array();
        $query['tag_id'] = $tag_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function syncCorpTagList($authorizer_appid, $provider_appid, $res, $now)
    {
        if (!empty($res['tag_group'])) {
            foreach ($res['tag_group'] as $tagGroupInfo) {

                // "group_id": "TAG_GROUPID1",
                // "group_name": "GOURP_NAME",
                // "create_time": 1557838797,
                // "order": 1,
                // "deleted": false,
                foreach ($tagGroupInfo['tag'] as $tagInfo) {

                    // "id": "TAG_ID1",
                    // "name": "NAME1",
                    // "create_time": 1557838797,
                    // "order": 1,
                    // "deleted": false
                    $tag_id = $tagInfo['id'];
                    $info = $this->getInfoByCorpTag($tag_id, $authorizer_appid);
                    $data = array();
                    $data['tag_name'] = $tagInfo['name'];
                    $data['tag_create_time'] = \App\Common\Utils\Helper::getCurrentTime($tagInfo['create_time']);
                    $data['tag_order'] = $tagInfo['order'];
                    $data['tag_deleted'] = (isset($tagInfo['deleted']) ? intval($tagInfo['deleted']) : 0);
                    $data['tag_group_id'] = $tagGroupInfo['group_id'];
                    $data['tag_group_name'] = $tagGroupInfo['group_name'];
                    $data['tag_group_create_time'] = \App\Common\Utils\Helper::getCurrentTime($tagGroupInfo['create_time']);
                    $data['tag_group_order'] = $tagGroupInfo['order'];
                    $data['tag_group_deleted'] = (isset($tagGroupInfo['deleted']) ? intval($tagGroupInfo['deleted']) : 0);
                    $data['provider_appid'] = $provider_appid;
                    $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                    if (!empty($info)) {
                        $this->update(array('_id' => $info['_id']), array('$set' => $data));
                    } else {
                        $data['authorizer_appid'] = $authorizer_appid;
                        $data['tag_id'] = $tag_id;
                        $this->insert($data);
                    }
                }
            }
        }
    }
}
