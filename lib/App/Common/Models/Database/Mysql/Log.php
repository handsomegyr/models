<?php

namespace App\Common\Models\Database\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 数据库-用户行为日志表管理
     * This model is mapped to the table idatabase_log
     */
    public function getSource()
    {
        return 'idatabase_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['session_info'] = $this->changeToArray($data['session_info']);
        $data['get_params'] = $this->changeToArray($data['get_params']);
        $data['post_params'] = $this->changeToArray($data['post_params']);
        $data['server_info'] = $this->changeToArray($data['server_info']);
        return $data;
    }
}
