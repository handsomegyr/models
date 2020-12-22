<?php

namespace App\Weixin2\Models\Miniprogram\SubscribeMsg\Template;

class Template extends \App\Common\Models\Weixin2\Miniprogram\SubscribeMsg\Template\Template
{

    /**
     * 根据微信模板id获取信息
     *
     * @param string $template_id            
     * @param string $authorizer_appid            
     * @param string $component_appid    
     */
    public function getInfoByTemplateId($template_id, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'template_id' => $template_id,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function updateCreatedStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 1;
        $updateData['template_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function removeCreatedStatus($id, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 0;
        return $this->update(array('_id' => $id), array('$set' => $updateData));
    }

    public function syncTemplateList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['data'])) {
            foreach ($res['data'] as $item) {
                $info = $this->getInfoByTemplateId($item['priTmplId'], $authorizer_appid, $component_appid);
                $data = array();
                // "priTmplId": "9Aw5ZV1j9xdWTFEkqCpZ7mIBbSC34khK55OtzUPl0rU",
                // "title": "领取奖金提醒",
                // "content": "会议时间:{{date2.DATA}}\n会议地点:{{thing1.DATA}}\n",
                // "example": "会议时间:2016年8月8日\n会议地点:TIT会议室\n"
                // "type": 2
                $data['title'] = $item['title'];
                $data['content'] = $item['content'];
                $data['example'] = $item['example'];
                $data['type'] = $item['type'];
                $data['is_created'] = 1;
                $data['template_time'] = \App\Common\Utils\Helper::getCurrentTime($now);

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['template_id'] = $item['template_id'];
                    $this->insert($data);
                }
            }
        }
    }
}
