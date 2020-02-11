<?php

namespace App\Weixin2\Models\Template;

class Template extends \App\Common\Models\Weixin2\Template\Template
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
        $info = $this->getModel()
            ->where('template_id', $template_id)
            ->where('authorizer_appid', $authorizer_appid)
            ->where('component_appid', $component_appid)
            ->first();
        $info = $this->getReturnData($info);

        return $info;
    }

    public function updateCreatedStatus($id, $res, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 1;
        $updateData['template_time'] = date("Y-m-d H:i:s", $now);
        return $this->updateById($id, $updateData);
    }

    public function removeCreatedStatus($id, $now)
    {
        $updateData = array();
        $updateData['is_created'] = 0;
        return $this->updateById($id, $updateData);
    }

    public function syncTemplateList($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['template_list'])) {
            foreach ($res['template_list'] as $item) {
                $info = $this->getInfoByTemplateId($item['template_id'], $authorizer_appid, $component_appid);
                $data = array();
                // "template_id": "iPk5sOIt5X_flOVKn5GrTFpncEYTojx6ddbt8WYoV5s",
                // "title": "领取奖金提醒",
                // "primary_industry": "IT科技",
                // "deputy_industry": "互联网|电子商务",
                // "content": "{ {result.DATA} }\n\n领奖金额:{ {withdrawMoney.DATA} }\n领奖 时间: { {withdrawTime.DATA} }\n银行信息:{ {cardInfo.DATA} }\n到账时间: { {arrivedTime.DATA} }\n{ {remark.DATA} }",
                // "example": "您已提交领奖申请\n\n领奖金额：xxxx元\n领奖时间：2013-10-10 12:22:22\n银行信息：xx银行(尾号xxxx)\n到账时间：预计xxxxxxx\n\n预计将于xxxx到达您的银行卡"
                $data['title'] = $item['title'];
                $data['primary_industry'] = $item['primary_industry'];
                $data['deputy_industry'] = $item['deputy_industry'];
                $data['content'] = $item['content'];
                $data['example'] = $item['example'];
                $data['is_created'] = 1;
                $data['template_time'] = date("Y-m-d H:i:s", $now);

                if (!empty($info)) {
                    $this->updateById($info['id'], $data);
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
