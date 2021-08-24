<?php

namespace App\Common\Models\Qyweixin\Mysql\MsgAudit;

use App\Common\Models\Base\Mysql\Base;

class Maxseq extends Base
{
    /**
     * 企业微信-会话内容存档-最大SEQ
     * This model is mapped to the table iqyweixin_msgaudit_maxseq
     */
    public function getSource()
    {
        return 'iqyweixin_msgaudit_maxseq';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['max_seq_got_time'] = $this->changeToValidDate($data['max_seq_got_time']);
        return $data;
    }
}
