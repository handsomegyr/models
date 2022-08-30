<?php

namespace App\Qyweixin\Models\MsgAudit;

class Sn extends \App\Common\Models\Qyweixin\MsgAudit\Sn
{
    /**
     * 根据企业微信appidID获取列表
     * 
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid         
     */
    public function getListByCorpid($agentid, $authorizer_appid, $provider_appid)
    {
        $list = $this->findAll(array(
            'agentid' => $agentid,
            'authorizer_appid' => $authorizer_appid,
            'provider_appid' => $provider_appid
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
