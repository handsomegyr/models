<?php

namespace App\Qyweixin\Models\ExternalContact;

class MomentTaskUser extends \App\Common\Models\Qyweixin\ExternalContact\MomentTaskUser
{
    /**
     * 根据用户ID和朋友圈id获取信息
     *
     * @param string $userid 
     * @param string $moment_id
     * @param string $authorizer_appid
     * @param string $provider_appid         
     */
    public function getInfoByUserIdAndMomentId($userid, $moment_id, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['userid'] = $userid;
        $query['moment_id'] = $moment_id;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);
        return $info;
    }

    public function syncMomentTaskList($moment_id, $authorizer_appid, $provider_appid, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "next_cursor":"CURSOR",
        //     "task_list":[
        //         {
        //             "userid":"zhangsan",
        //             "publish_status":1
        //         }
        //     ]
        // }
        if (!empty($res['task_list'])) {
            foreach ($res['task_list'] as $task) {
                $userid = $task['userid'];
                $moment_id = $task['moment_id'];
                $publish_status = $task['publish_status'];
                $info = $this->getInfoByUserIdAndMomentId($userid, $moment_id, $authorizer_appid, $provider_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['publish_status'] = $publish_status;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['moment_id'] = $moment_id;
                    $data['userid'] = $userid;
                    $this->insert($data);
                }
            }
        }
    }
}
