<?php

namespace App\Weixin2\Services\Traits;

trait UserTrait
{

    public function getUserInfo($userInfo)
    {
        $modelUser = new \App\Weixin2\Models\User\User();
        $res = $this->getWeixinObject()
            ->getUserManager()
            ->getUserInfo($userInfo['openid']);
        /**
         * {
         * "subscribe": 1,
         * "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",
         * "nickname": "Band",
         * "sex": 1,
         * "language": "zh_CN",
         * "city": "广州",
         * "province": "广东",
         * "country": "中国",
         * "headimgurl":"http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
         * "subscribe_time": 1382694957,
         * "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
         * "remark": "",
         * "groupid": 0,
         * "tagid_list":[128,2],
         * "subscribe_scene": "ADD_SCENE_QR_CODE",
         * "qr_scene": 98765,
         * "qr_scene_str": ""
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUser->updateUserInfoById($userInfo, $res);
        return $res;
    }

    public function getUserTagIdList($user_id)
    {
        $modelUser = new \App\Weixin2\Models\User\User();
        $userInfo = $modelUser->getInfoById($user_id);
        if (empty($userInfo)) {
            throw new \Exception("用户记录ID:{$user_id}所对应的用户不存在");
        }
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->userTagList($userInfo['openid']);
        /**
         * {
         * "tagid_list":[128,2]
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUser->updateUserInfoById($userInfo, $res);
        return $res;
    }

    public function addUserTag($user_tag_id)
    {
        $modelUserTag = new \App\Weixin2\Models\User\Tag();
        $userTagInfo = $modelUserTag->getInfoById($user_tag_id);
        if (empty($userTagInfo)) {
            throw new \Exception("用户标签记录ID:{$user_tag_id}所对应的用户标签不存在");
        }
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->create($userTagInfo['name']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * { "tag":{ "id":134,//标签id "name":"广东" } }
         */
        $modelUserTag->recordTagId($user_tag_id, $res, time());
        return $res;
    }

    public function deleteUserTag($user_tag_id)
    {
        $modelUserTag = new \App\Weixin2\Models\User\Tag();
        $userTagInfo = $modelUserTag->getInfoById($user_tag_id);
        if (empty($userTagInfo)) {
            throw new \Exception("用户标签记录ID:{$user_tag_id}所对应的用户标签不存在");
        }
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->delete($userTagInfo['tag_id']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUserTag->removeTagId($user_tag_id);

        return $res;
    }

    public function syncTagList()
    {
        $modelUserTag = new \App\Weixin2\Models\User\Tag();
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->get();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "tags":[{
         * "id":1,
         * "name":"每天一罐可乐星人",
         * "count":0 //此标签下粉丝数
         * },
         * {
         * "id":2,
         * "name":"星标组",
         * "count":0
         * },
         * {
         * "id":127,
         * "name":"广东",
         * "count":5
         * }
         * ] }
         */
        $modelUserTag->syncTagList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function tagUser($user_to_usertag_id)
    {
        $modelUserToUserTag = new \App\Weixin2\Models\User\UserToUserTag();
        $userToTagInfo = $modelUserToUserTag->getInfoById($user_to_usertag_id);
        if (empty($userToTagInfo)) {
            throw new \Exception("用户和用户标签对应记录ID:{$user_to_usertag_id}所对应的记录不存在");
        }
        $openidList = array();
        $openidList[] = $userToTagInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchtagging($userToTagInfo['tag_id'], $openidList);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelUserToUserTag->tag($user_to_usertag_id, $res, time());
        return $res;
    }

    public function untagUser($user_to_usertag_id)
    {
        $modelUserToUserTag = new \App\Weixin2\Models\User\UserToUserTag();
        $userToTagInfo = $modelUserToUserTag->getInfoById($user_to_usertag_id);
        if (empty($userToTagInfo)) {
            throw new \Exception("用户和用户标签对应记录ID:{$user_to_usertag_id}所对应的记录不存在");
        }
        $openidList = array();
        $openidList[] = $userToTagInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchuntagging($userToTagInfo['tag_id'], $openidList);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelUserToUserTag->untag($user_to_usertag_id, time());

        return $res;
    }

    public function blackUser($black_user_id)
    {
        $modelBlackUser = new \App\Weixin2\Models\User\BlackUser();
        $blackUserInfo = $modelBlackUser->getInfoById($black_user_id);
        if (empty($blackUserInfo)) {
            throw new \Exception("黑名单用户对应记录ID:{$black_user_id}所对应的记录不存在");
        }
        $openid_list = array();
        $openid_list[] = $blackUserInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchblacklist($openid_list);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelBlackUser->black($black_user_id, $res, time());
        return $res;
    }

    public function unblackUser($black_user_id)
    {
        $modelBlackUser = new \App\Weixin2\Models\User\BlackUser();
        $blackUserInfo = $modelBlackUser->getInfoById($black_user_id);
        if (empty($blackUserInfo)) {
            throw new \Exception("黑名单用户对应记录ID:{$black_user_id}所对应的记录不存在");
        }
        $openid_list = array();
        $openid_list[] = $blackUserInfo['openid'];
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->batchunblacklist($openid_list);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelBlackUser->unblack($black_user_id, time());

        return $res;
    }

    public function syncBlackList()
    {
        $modelBlackUser = new \App\Weixin2\Models\User\BlackUser();
        $res = $this->getWeixinObject()
            ->getTagsManager()
            ->getblacklist();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "total":23000,
         * "count":10000,
         * "data":{"
         * openid":[
         * "OPENID1",
         * "OPENID2",
         * ...,
         * "OPENID10000"
         * ]
         * },
         * "next_openid":"OPENID10000"
         * }
         */
        $modelBlackUser->syncBlackList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncSubscribeUserList($now, $openid4Test)
    {
        $modelSubscribeUser = new \App\Weixin2\Models\User\SubscribeUser();
        $userManager = $this->getWeixinObject()->getUserManager();

        // 先确认accesstoken没有问题
        $FromUserName = $openid4Test;
        $userInfo = $userManager->getUserInfo($FromUserName);
        if (!empty($userInfo['errcode'])) {
            throw new \Exception($userInfo['errmsg'], $userInfo['errcode']);
        }

        // 清空数据
        $query = array(
            'authorizer_appid' => $this->authorizer_appid,
            'component_appid' => $this->component_appid
        );
        $this->modelSubscribeUser->physicalRemove($query);

        // 参数 说明
        $total = 0; // 关注该公众账号的总用户数
        $count = 0; // 拉取的OPENID个数，最大值为10000
        $data = array(); // 列表数据，OPENID的列表
        $next_openid = ""; // 拉取列表的最后一个用户的OPENID
        for ($i = 1; $i > 0;) {

            $ret = $userManager->getUser($next_openid);
            if (!empty($ret['errcode'])) {
                throw new \Exception($ret['errmsg'], $ret['errcode']);
            }

            $total = $ret['total']; // 关注该公众账号的总用户数
            $count = $count + $ret['count']; // 拉取的OPENID个数，最大值为10000
            $data = empty($ret['data']) ? array() : $ret['data']['openid']; // 列表数据，OPENID的列表
            $next_openid = $ret['next_openid']; // 拉取列表的最后一个用户的OPENID

            if (!empty($data)) {
                foreach ($data as  $openid) {
                    $modelSubscribeUser->log($this->authorizer_appid, $this->component_appid, $openid, $now);
                }
            }

            // 按以下的检查判断是否要退出循环
            if (empty($data)) {
                break;
            }

            if (empty($next_openid)) {
                break;
            }

            if ($count >= $total) {
                break;
            }
        }
    }
}
