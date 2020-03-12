<?php

namespace App\Weixin2\Models\Kf;

class MsgRecord extends \App\Common\Models\Weixin2\Kf\MsgRecord
{

    /**
     * 根据WX记录信息获取信息
     *
     * @param string $worker            
     * @param string $openid            
     * @param string $opercode            
     * @param string $msgrecord_time            
     * @param string $authorizer_appid            
     * @param string $component_appid            
     */
    public function getInfoByRecord($worker, $openid, $opercode, $msgrecord_time, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'worker' => $worker,
            'openid' => $openid,
            'msgrecord_time' => $msgrecord_time,
            'opercode' => $opercode,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncMsgRecordList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['recordlist'])) {
            foreach ($res['recordlist'] as $item) {
                $data = array();
                /**
                 * "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk" ,
                 * "opercode" : 2002,
                 * "text" : " 您好，客服test1为您服务。" ,
                 * "time" : 1400563710,
                 * "worker" : "test1@test"
                 */
                $data['worker'] = $item['worker'];
                $data['openid'] = $item['openid'];
                $data['opercode'] = $item['opercode'];
                $data['text'] = $item['text'];
                $data['msgrecord_time'] = getCurrentTime($item['time']);

                $info = $this->getInfoByRecord($item['worker'], $item['openid'], $item['opercode'], $item['msgrecord_time'], $authorizer_appid, $component_appid);

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $this->insert($data);
                }
            }
        }
    }
}
