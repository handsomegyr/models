<?php

namespace App\Qyweixin\Models\ExternalContact;

class CorpTagGroup extends \App\Common\Models\Qyweixin\ExternalContact\CorpTagGroup
{

    /**
     * 根据tag_group_id获取信息
     *
     * @param string $tag_group_id            
     * @param string $authorizer_appid          
     */
    public function getInfoByGroupId($tag_group_id, $authorizer_appid)
    {
        $query = array();
        $query['tag_group_id'] = $tag_group_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($authorizer_appid, $provider_appid, $now)
    {
        $updateData = array('is_exist' => 0);
        $updateData['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(
            array(
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncCorpTagGroupList($authorizer_appid, $provider_appid, $res, $now)
    {
        // {
        //     "errcode": 0,
        //     "errmsg": "ok",
        //     "tag_group": [{
        //         "group_id": "TAG_GROUPID1",
        //         "group_name": "GOURP_NAME",
        //         "create_time": 1557838797,
        //         "order": 1,
        //         "deleted": false,
        //         "tag": [{
        //                 "id": "TAG_ID1",
        //                 "name": "NAME1",
        //                 "create_time": 1557838797,
        //                 "order": 1,
        //                 "deleted": false
        //             },
        //             {
        //                 "id": "TAG_ID2",
        //                 "name": "NAME2",
        //                 "create_time": 1557838797,
        //                 "order": 2,
        //                 "deleted": true
        //             }
        //         ]
        //     }]
        // }
        if (!empty($res['tag_group'])) {
            foreach ($res['tag_group'] as $tagGroupInfo) {

                $tag_group_id = $tagGroupInfo['group_id'];
                $info = $this->getInfoByGroupId($tag_group_id, $authorizer_appid);
                $data = array();
                $data['tag_group_id'] = $tagGroupInfo['group_id'];
                $data['tag_group_name'] = $tagGroupInfo['group_name'];
                $data['tag_group_create_time'] = \App\Common\Utils\Helper::getCurrentTime($tagGroupInfo['create_time']);
                $data['tag_group_order'] = $tagGroupInfo['order'];
                $data['tag_group_deleted'] = (isset($tagGroupInfo['deleted']) ? intval($tagGroupInfo['deleted']) : 0);
                $data['provider_appid'] = $provider_appid;
                // 通过这个字段来表明企业微信那边有这条记录
                $data['is_exist'] = 1;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['tag_group_id'] = $tag_group_id;
                    $this->insert($data);
                }
            }
        }
    }
}
