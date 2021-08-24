<?php

namespace App\Common\Models\Qyweixin\Mysql\MsgAudit;

use App\Common\Models\Base\Mysql\Base;

class Chatdata extends Base
{
    /**
     * 企业微信-会话内容存档-会话内容
     * This model is mapped to the table iqyweixin_msgaudit_chatdata
     */
    public function getSource()
    {
        return 'iqyweixin_msgaudit_chatdata';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['tolist'] = $this->changeToArray($data['tolist']);
        $data['msgtime'] = $this->changeToValidDate($data['msgtime']);
        $data['agree_agree_time'] = $this->changeToValidDate($data['agree_agree_time']);
        $data['chatrecord_item'] = $this->changeToArray($data['chatrecord_item']);
        $data['item_content'] = $this->changeToArray($data['item_content']);

        $data['item_from_chatroom'] = $this->changeToBoolean($data['item_from_chatroom']);
        $data['collect_create_time'] = $this->changeToValidDate($data['collect_create_time']);
        $data['collect_details'] = $this->changeToArray($data['item_content']);

        $data['meeting_starttime'] = $this->changeToValidDate($data['meeting_starttime']);
        $data['meeting_endtime'] = $this->changeToValidDate($data['meeting_endtime']);
        $data['switch_time'] = $this->changeToValidDate($data['switch_time']);

        $data['calendar_starttime'] = $this->changeToValidDate($data['calendar_starttime']);
        $data['calendar_endtime'] = $this->changeToValidDate($data['calendar_endtime']);
        $data['mixed_mixed'] = $this->changeToArray($data['mixed_mixed']);

        $data['meeting_voice_call_endtime'] = $this->changeToValidDate($data['meeting_voice_call_endtime']);

        $data['meeting_voice_call_demofiledata'] = $this->changeToArray($data['meeting_voice_call_demofiledata']);
        $data['meeting_voice_call_sharescreendata'] = $this->changeToArray($data['meeting_voice_call_sharescreendata']);
        return $data;
    }
}
