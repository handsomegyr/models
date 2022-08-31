<?php

namespace App\Qyweixin\Services\Traits;

trait ContactTrait
{
    // 读取成员
    public function getUserInfo($userInfo)
    {
        $modelUser = new \App\Qyweixin\Models\Contact\User();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->get($userInfo['userid']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userid": "zhangsan",
         * "name": "李四",
         * "department": [1, 2],
         * "order": [1, 2],
         * "position": "后台工程师",
         * "mobile": "13800000000",
         * "gender": "1",
         * "email": "zhangsan@gzdev.com",
         * "is_leader_in_dept": [1, 0],
         * "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0",
         * "thumb_avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/100",
         * "telephone": "020-123456",
         * "alias": "jackzhang",
         * "address": "广州市海珠区新港中路",
         * "open_userid": "xxxxxx",
         * "main_department": 1,
         * "extattr": {
         * "attrs": [
         * {
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * }
         * ]
         * },
         * "status": 1,
         * "qr_code": "https://open.work.weixin.qq.com/wwopen/userQRCode?vcode=xxx",
         * "external_position": "产品经理",
         * "external_profile": {
         * "external_corp_name": "企业简称",
         * "external_attr": [{
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * },
         * {
         * "type": 2,
         * "name": "测试app",
         * "miniprogram": {
         * "appid": "wx8bd80126147dFAKE",
         * "pagepath": "/index",
         * "title": "my miniprogram"
         * }
         * }
         * ]
         * }
         * }
         */
        if (!empty($res['errcode'])) {
            // [errmsg] => userid not found, hint: [1646185148234890469598501], from ip: 115.29.169.68, more info at https://open.work.weixin.qq.com/devtool/query?e=60111
            if ($res['errcode'] == 60111) {
            } else {
                throw new \Exception($res['errmsg'], $res['errcode']);
            }
        }
        $modelUser->updateUserInfoById($userInfo, $res, time());
        return $res;
    }

    // userid转openid
    public function convertToOpenid($userInfo)
    {
        $modelUser = new \App\Qyweixin\Models\Contact\User();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->convertToOpenid($userInfo['userid']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "openid": "oDjGHs-1yCnGrRovBj2yHij5JAAA"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $data = array();
        $data['openid'] = $res['openid'];
        $modelUser->update(array('_id' => $userInfo['_id']), array('$set' => $data));
        return $res;
    }

    // openid转userid
    public function convertToUserid($userInfo)
    {
        $modelUser = new \App\Qyweixin\Models\Contact\User();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->convertToUserid($userInfo['openid']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userid": "zhangsan"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $data = array();
        $data['userid'] = $res['userid'];
        $modelUser->update(array('_id' => $userInfo['_id']), array('$set' => $data));
        return $res;
    }

    // 获取加入企业二维码
    public function getJoinQrcode($qrcodeInfo)
    {
        $modelCorpJoinQrcode = new \App\Qyweixin\Models\Contact\CorpJoinQrcode();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->corpGetJoinQrcode($qrcodeInfo['size_type']);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "join_qrcode": "https://work.weixin.qq.com/wework_admin/genqrcode?action=join&vcode=3db1fab03118ae2aa1544cb9abe84&r=hb_share_api_mjoin&qr_size=3"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelCorpJoinQrcode->recordJoinQrcode($qrcodeInfo['_id'], $res, time());
        return $res;
    }

    // 批量邀请成员
    public function batchInvite($batchInviteInfo)
    {
        $modelBatchInvite = new \App\Qyweixin\Models\Contact\BatchInvite();
        $user = empty($batchInviteInfo['user']) ? array() : (is_array($batchInviteInfo['user']) ? $batchInviteInfo['user'] : \json_decode($batchInviteInfo['user'], true));
        $party = empty($batchInviteInfo['party']) ? array() : (is_array($batchInviteInfo['party']) ? $batchInviteInfo['party'] : \json_decode($batchInviteInfo['party'], true));
        $tag = empty($batchInviteInfo['tag']) ? array() : (is_array($batchInviteInfo['tag']) ? $batchInviteInfo['tag'] : \json_decode($batchInviteInfo['tag'], true));
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->batchInvite($user, $party, $tag);
        /**
         * {
         * "errcode" : 0,
         * "errmsg" : "ok",
         * "invaliduser" : ["UserID1", "UserID2"],
         * "invalidparty" : [PartyID1, PartyID2],
         * "invalidtag": [TagID1, TagID2]
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelBatchInvite->recordResult($batchInviteInfo['_id'], $res, time());
        return $res;
    }

    //获取部门列表
    public function getDepartmentList($dep_id)
    {
        $modelDepartment = new \App\Qyweixin\Models\Contact\Department();
        $modelDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
        $res = $this->getQyWeixinObject()
            ->getDepartmentManager()
            ->getDepartmentList($dep_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "department": [
         *  {
         *      "id": 2,
         *      "name": "广州研发中心",
         *      "name_en": "RDGZ",
         *      "parentid": 1,
         *      "order": 10
         *  },
         *  {
         *      "id": 3,
         *      "name": "邮箱产品部",
         *      "name_en": "mail",
         *      "parentid": 2,
         *      "order": 40
         *  }
         *]
         * }
         */
        // 如果从跟部门进行同步的话 那么先将所有的记录is_exist改成0
        $now = time();
        if (empty($dep_id)) {
            $modelDepartment->clearExist($this->authorizer_appid, $this->provider_appid, $this->agentid, $now);
            $modelDepartmentUser->clearExist($this->authorizer_appid, $this->provider_appid, $this->agentid, $now);
        }
        $modelDepartment->syncDepartmentList($this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);
        return $res;
    }

    // 获取部门成员
    public function getDepartmentUserSimplelist($dep_id, $fetch_child = 0, $is_root = false)
    {
        $modelUser = new \App\Qyweixin\Models\Contact\User();
        $modelDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->simplelist($dep_id, $fetch_child);
        if (!empty($res['errcode'])) {
            //https://open.work.weixin.qq.com/devtool/query?e=60003
            if ($res['errcode'] == 60003) {
                return $res;
            }
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userlist": [
         * {
         * "userid": "zhangsan",
         * "name": "李四",
         * "department": [1, 2],
         * "open_userid": "xxxxxx"
         * }
         * ]}
         */
        $now = time();
        if (!empty($is_root) && !empty($fetch_child)) {
            $modelDepartmentUser->clearExist($this->authorizer_appid, $this->provider_appid, $this->agentid, $now);
        }
        $modelDepartmentUser->syncDepartmentUserList($this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);
        $modelUser->syncUserList($this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);

        return $res;
    }

    // 获取部门成员详情
    public function getDepartmentUserDetaillist($dep_id, $fetch_child = 0, $is_root = false)
    {
        $modelUser = new \App\Qyweixin\Models\Contact\User();
        $modelDepartmentUser = new \App\Qyweixin\Models\Contact\DepartmentUser();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->userlist($dep_id, $fetch_child);
        if (!empty($res['errcode'])) {
            //https://open.work.weixin.qq.com/devtool/query?e=60003
            if ($res['errcode'] == 60003) {
                return $res;
            }
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "userlist": [{
         * "userid": "zhangsan",
         * "name": "李四",
         * "department": [1, 2],
         * "order": [1, 2],
         * "position": "后台工程师",
         * "mobile": "13800000000",
         * "gender": "1",
         * "email": "zhangsan@gzdev.com",
         * "is_leader_in_dept": [1, 0],
         * "avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0",
         * "thumb_avatar": "http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/100",
         * "telephone": "020-123456",
         * "alias": "jackzhang",
         * "status": 1,
         * "address": "广州市海珠区新港中路",
         * "hide_mobile" : 0,
         * "english_name" : "jacky",
         * "open_userid": "xxxxxx",
         * "main_department": 1,
         * "extattr": {
         * "attrs": [
         * {
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * }
         * ]
         * },
         * "qr_code": "https://open.work.weixin.qq.com/wwopen/userQRCode?vcode=xxx",
         * "external_position": "产品经理",
         * "external_profile": {
         * "external_corp_name": "企业简称",
         * "external_attr": [{
         * "type": 0,
         * "name": "文本名称",
         * "text": {
         * "value": "文本"
         * }
         * },
         * {
         * "type": 1,
         * "name": "网页名称",
         * "web": {
         * "url": "http://www.test.com",
         * "title": "标题"
         * }
         * },
         * {
         * "type": 2,
         * "name": "测试app",
         * "miniprogram": {
         * "appid": "wx8bd80126147dFAKE",
         * "pagepath": "/index",
         * "title": "miniprogram"
         * }
         * }
         * ]
         * }
         * }]
         * }
         */
        $now = time();
        if (!empty($is_root) && !empty($fetch_child)) {
            $modelDepartmentUser->clearExist($this->authorizer_appid, $this->provider_appid, $this->agentid, $now);
        }
        $modelDepartmentUser->syncDepartmentUserList($this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);
        $modelUser->syncUserList($this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);

        return $res;
    }

    //获取标签列表
    public function getTagList()
    {
        $modelTag = new \App\Qyweixin\Models\Contact\Tag();
        $res = $this->getQyWeixinObject()
            ->getTagManager()
            ->getTagList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "taglist":[
         *      {"tagid":1,"tagname":"a"},
         *      {"tagid":2,"tagname":"b"}
         *  ]
         * }
         */
        $modelTag->syncTagList($this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取标签成员
    public function getTag($tagid)
    {
        $res = $this->getQyWeixinObject()
            ->getTagManager()
            ->get($tagid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "tagname": "乒乓球协会",
         * "userlist": [
         * {
         *  "userid": "zhangsan",
         *  "name": "李四"
         * }
         * ],
         * "partylist": [2]
         * }
         */

        $modelTagParty = new \App\Qyweixin\Models\Contact\TagParty();
        $modelTagUser = new \App\Qyweixin\Models\Contact\TagUser();
        $now = time();
        $modelTagParty->syncTagDepartmentList($tagid, $this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);
        $modelTagUser->syncTagUserList($tagid, $this->authorizer_appid, $this->provider_appid, $this->agentid, $res, $now);
        return $res;
    }

    //获取企业活跃成员数
    public function getActiveStat($start_time)
    {
        $modelUserActiveStat = new \App\Qyweixin\Models\Contact\UserActiveStat();
        $res = $this->getQyWeixinObject()
            ->getUserManager()
            ->getActiveStat($start_time);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "active_cnt":100
         * }
         */
        $modelUserActiveStat->syncActiveStat($start_time, $this->agentid, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }
}
