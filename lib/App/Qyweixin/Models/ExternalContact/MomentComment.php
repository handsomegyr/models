<?php

namespace App\Qyweixin\Models\ExternalContact;

class MomentComment extends \App\Common\Models\Qyweixin\ExternalContact\MomentComment
{
    /**
     * 根据用户ID和朋友圈id获取信息
     *
     * @param string $behavior_userid 
     * @param string $behavior_create_time
     * @param string $behavior_user_type
     * @param string $behavior
     * @param string $userid 
     * @param string $moment_id
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid         
     */
    public function getInfoByUserIdAndMomentId($behavior_userid, $behavior_create_time, $behavior_user_type, $behavior, $userid, $moment_id, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['behavior_userid'] = $behavior_userid;
        $query['behavior_create_time'] = \App\Common\Utils\Helper::getCurrentTime($behavior_create_time);
        $query['behavior_user_type'] = $behavior_user_type;
        $query['behavior'] = $behavior;
        $query['userid'] = $userid;
        $query['moment_id'] = $moment_id;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);
        return $info;
    }

    public function syncMomentBehaviorList($userid, $moment_id, $agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "comment_list":[
        //         {
        //             "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACAAAA ",
        //             "create_time":1605172726
        //         },
        //         {
        //             "userid":"zhangshan ",
        //             "create_time":1605172729
        //         }
        //     ],
        //     "like_list":[
        //         {
        //             "external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACBBBB ",
        //             "create_time":1605172726
        //         },
        //         {
        //             "userid":"zhangshan ",
        //             "create_time":1605172720
        //         }
        //     ]
        // }
        if (!empty($res['comment_list'])) {
            foreach ($res['comment_list'] as $behaviorInfo) {
                $this->recordBehaviorInfo($userid, $moment_id, "comment", $behaviorInfo, $agentid, $authorizer_appid, $provider_appid, $now);
            }
        }
        if (!empty($res['like_list'])) {
            foreach ($res['like_list'] as $behaviorInfo) {
                $this->recordBehaviorInfo($userid, $moment_id, "like", $behaviorInfo, $agentid, $authorizer_appid, $provider_appid, $now);
            }
        }
    }

    private function recordBehaviorInfo($userid, $moment_id, $behavior, $behaviorInfo, $agentid, $authorizer_appid, $provider_appid, $now)
    {
        $userid4act = isset($behaviorInfo['userid']) ? $behaviorInfo['userid'] : '';
        $external_userid4act = isset($behaviorInfo['external_userid']) ? $behaviorInfo['external_userid'] : '';
        $behavior_create_time = $behaviorInfo['create_time'];
        $behavior_userid = empty($userid) ? $external_userid4act : $userid4act;
        $behavior_user_type = empty($userid) ? 'external_userid' : 'userid';
        $info = $this->getInfoByUserIdAndMomentId($behavior_userid, $behavior_create_time, $behavior_user_type, $behavior, $userid, $moment_id, $agentid, $authorizer_appid, $provider_appid);
        $data = array();
        $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        if (!empty($info)) {
            $this->update(array('_id' => $info['_id']), array('$set' => $data));
        } else {
            $data['provider_appid'] = $provider_appid;
            $data['authorizer_appid'] = $authorizer_appid;
            $data['agentid'] = $agentid;
            $data['moment_id'] = $moment_id;
            $data['userid'] = $userid;
            $data['behavior'] = $behavior;
            $data['behavior_user_type'] = $behavior_user_type;
            $data['behavior_create_time'] = \App\Common\Utils\Helper::getCurrentTime($behavior_create_time);
            $data['behavior_userid'] = $behavior_userid;
            $this->insert($data);
        }
    }
}
