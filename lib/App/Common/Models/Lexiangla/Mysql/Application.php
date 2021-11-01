<?php

namespace App\Common\Models\Lexiangla\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Application extends Base
{

    /**
     * 乐享-应用管理
     * This model is mapped to the table ilexiangla_application
     */
    public function getSource()
    {
        return 'ilexiangla_application';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['access_token_expire'] = $this->changeToValidDate($data['access_token_expire']);
        $data['jsapi_ticket_expire'] = $this->changeToValidDate($data['jsapi_ticket_expire']);
        return $data;
    }
}
