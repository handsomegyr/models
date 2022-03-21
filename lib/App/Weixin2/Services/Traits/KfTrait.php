<?php

namespace App\Weixin2\Services\Traits;

trait KfTrait
{

    public function addOrUpdateKfAccount($kfaccount_id, $is_add = true)
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $kfAccountInfo = $modelKfAccount->getInfoById($kfaccount_id);
        if (empty($kfAccountInfo)) {
            throw new \Exception("客服帐号记录ID:{$kfaccount_id}所对应的记录不存在");
        }
        if ($is_add) {
            $method = "kfaccountAdd";
        } else {
            $method = "kfaccountUpdate";
        }

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->$method($kfAccountInfo['kf_account'], $kfAccountInfo['nickname'], $kfAccountInfo['password']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelKfAccount->updateCreatedStatus($kfaccount_id, $res, time());
        return $res;
    }

    public function deleteKfAccount($kfaccount_id)
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $kfAccountInfo = $modelKfAccount->getInfoById($kfaccount_id);
        if (empty($kfAccountInfo)) {
            throw new \Exception("客服帐号记录ID:{$kfaccount_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->kfaccountDel($kfAccountInfo['kf_account']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelKfAccount->removeCreatedStatus($kfaccount_id, time());

        return $res;
    }

    public function syncKfAccountList()
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->getkflist();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "kf_list": [
         * {
         * "kf_account": "test1@test",
         * "kf_nick": "ntest1",
         * "kf_id": "1001"，
         * "kf_headimgurl": " http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw/0"
         * },
         * {
         * "kf_account": "test2@test",
         * "kf_nick": "ntest2",
         * "kf_id": "1002"，
         * "kf_headimgurl": " http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw /0"
         * },
         * {
         * "kf_account": "test3@test",
         * "kf_nick": "ntest3",
         * "kf_id": "1003"，
         * "kf_headimgurl": " http://mmbiz.qpic.cn/mmbiz/4whpV1VZl2iccsvYbHvnphkyGtnvjfUS8Ym0GSaLic0FD3vN0V8PILcibEGb2fPfEOmw /0"
         * }
         * ]
         * }
         */
        $modelKfAccount->syncKfAccountList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncOnlineKfAccountList()
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->getonlinekflist();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "kf_online_list" : [
         * {
         * "kf_account" :
         * "test1@test" ,
         * "status" : 1,
         * "kf_id" :
         * "1001" ,
         * "accepted_case" : 1
         * },
         * {
         * "kf_account" :
         * "test2@test" ,
         * "status" : 1,
         * "kf_id" :
         * "1002" ,
         * "accepted_case" : 2
         * }
         * ]
         * }
         */
        $modelKfAccount->syncOnlineKfAccountList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }
    public function inviteWorker($kfaccount_id)
    {
        $modelKfAccount = new \App\Weixin2\Models\Kf\Account();
        $kfAccountInfo = $modelKfAccount->getInfoById($kfaccount_id);
        if (empty($kfAccountInfo)) {
            throw new \Exception("客服帐号记录ID:{$kfaccount_id}所对应的记录不存在");
        }

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->inviteWorker($kfAccountInfo['kf_account'], $kfAccountInfo['invite_wx']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        return $res;
    }


    public function createKfSession($kfsession_id)
    {
        $modelKfSession = new \App\Weixin2\Models\Kf\Session();
        $kfSessionInfo = $modelKfSession->getInfoById($kfsession_id);
        if (empty($kfSessionInfo)) {
            throw new \Exception("客服会话记录ID:{$kfsession_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->kfsessionCreate($kfSessionInfo['kf_account'], $kfSessionInfo['openid']);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $modelKfSession->updateCreatedStatus($kfsession_id, $res, time());
        return $res;
    }

    public function closeKfSession($kfsession_id)
    {
        $modelKfSession = new \App\Weixin2\Models\Kf\Session();
        $kfSessionInfo = $modelKfSession->getInfoById($kfsession_id);
        if (empty($kfSessionInfo)) {
            throw new \Exception("客服会话记录ID:{$kfsession_id}所对应的记录不存在");
        }
        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->kfsessionClose($kfSessionInfo['kf_account'], $kfSessionInfo['openid']);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelKfSession->removeCreatedStatus($kfsession_id, time());

        return $res;
    }


    public function syncMsgRecordList($msgrecord_start_time, $msgrecord_end_time)
    {
        $modelMsgRecord = new \App\Weixin2\Models\Kf\MsgRecord();
        $starttime = strtotime(date('Y-m-d', $msgrecord_start_time) . " 00:00:00");
        $endtime = strtotime(date('Y-m-d', $msgrecord_end_time) . " 23:59:59");

        $res = $this->getWeixinObject()
            ->getCustomServiceManager()
            ->getMsgList($starttime, $endtime);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "recordlist" : [
         * {
         * "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk" ,
         * "opercode" : 2002,
         * "text" : " 您好，客服test1为您服务。" ,
         * "time" : 1400563710,
         * "worker" : "test1@test"
         * },
         * {
         * "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk" ,
         * "opercode" : 2003,
         * "text" : "你好，有什么事情？" ,
         * "time" : 1400563731,
         * "worker" : "test1@test"
         * }
         * ],
         * "number":2,
         * "msgid":20165267
         * }
         */
        $modelMsgRecord->syncMsgRecordList($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }
}
