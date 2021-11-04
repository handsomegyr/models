<?php

namespace App\Qyweixin\Models\MsgAudit;

class Sn extends \App\Common\Models\Qyweixin\MsgAudit\Sn
{
    /**
     * 根据企业微信appidID获取列表
     *         
     * @param string $corpid          
     */
    public function getListByCorpid($corpid)
    {
        $list = $this->findAll(array(
            'authorizer_appid' => $corpid
        ), array('publickey_ver' => 1, '_id' => 1));
        $ret = array();
        if (!empty($list)) {
            foreach ($list as $item) {
                $ret['publickey_ver:' . $item['publickey_ver']] = $item;
            }
        }
        return $ret;
    }
}
