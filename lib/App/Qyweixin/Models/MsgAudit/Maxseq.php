<?php

namespace App\Qyweixin\Models\MsgAudit;

class Maxseq extends \App\Common\Models\Qyweixin\MsgAudit\Maxseq
{
    /**
     * 根据企业微信appidID获取信息
     *         
     * @param string $corpid          
     */
    public function getInfoByCorpid($corpid)
    {
        $query = array(
            'authorizer_appid' => $corpid
        );
        $info = $this->findOne($query);
        return $info;
    }
}
