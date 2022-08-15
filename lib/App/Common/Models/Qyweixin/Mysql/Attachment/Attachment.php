<?php

namespace App\Common\Models\Qyweixin\Mysql\Attachment;

use App\Common\Models\Base\Mysql\Base;

class Attachment extends Base
{
    /**
     * 企业微信-附件资源
     * This model is mapped to the table iqyweixin_attachment
     */
    public function getSource()
    {
        return 'iqyweixin_attachment';
    }
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);

        $data['media_time'] = $this->changeToValidDate($data['media_time']);
        return $data;
    }
}
