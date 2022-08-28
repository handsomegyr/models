<?php

namespace App\Qyweixin\Services\Traits;

trait TransferTrait
{
    /**
     * 获取外部企业的联系人管理
     *
     * @return \Qyweixin\Manager\ExternalContact
     */
    public function getExternalContactManager()
    {
        return $this->getQyWeixinObject()->getExternalContactManager();
    }

    // 查询在职员工的客户接替状态
    public function getOnjobTransferResult($handover_userid, $takeover_userid, $cursor)
    {
        $modelOnjobTransferResult = new \App\Qyweixin\Models\ExternalContact\OnjobTransferResult();
        $res = $this->getExternalContactManager()->transferResult($handover_userid, $takeover_userid, $cursor);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //    "errcode": 0,
        //    "errmsg": "ok",
        //    "customer":
        //   [
        //   {
        //   	"external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACCCC",
        //   	"status":1,
        //   	"takeover_time":1588262400
        //   },
        //   {
        //   	"external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACBBBB",
        //   	"status":2,
        //   	"takeover_time":1588482400
        //   },
        //   {
        //   	"external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACAAAA",
        //   	"status":3,
        //   	"takeover_time":0
        //   }
        //   ],
        //   "next_cursor":"NEXT_CURSOR"
        // }
        $modelOnjobTransferResult->syncTransferResult($handover_userid, $takeover_userid, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 查询离职员工的客户接替状态
    public function getResignedTransferResult($handover_userid, $takeover_userid, $cursor)
    {
        $modelResignedTransferResult = new \App\Qyweixin\Models\ExternalContact\ResignedTransferResult();
        $res = $this->getExternalContactManager()->resignedTransferResult($handover_userid, $takeover_userid, $cursor);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //    "errcode": 0,
        //    "errmsg": "ok",
        //    "customer":
        //   [
        //   {
        //   	"external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACCCC",
        //   	"status":1,
        //   	"takeover_time":1588262400
        //   },
        //   {
        //   	"external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACBBBB",
        //   	"status":2,
        //   	"takeover_time":1588482400
        //   },
        //   {
        //   	"external_userid":"woAJ2GCAAAXtWyujaWJHDDGi0mACAAAA",
        //   	"status":3,
        //   	"takeover_time":0
        //   }
        //   ],
        //   "next_cursor":"NEXT_CURSOR"
        // }
        $modelResignedTransferResult->syncTransferResult($handover_userid, $takeover_userid, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }
}
