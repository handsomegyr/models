<?php

namespace App\Backend\Submodules\Qyweixin\Models\ReplyMsg;

class ReplyMsg extends \App\Common\Models\Qyweixin\ReplyMsg\ReplyMsg
{

    use \App\Backend\Models\Base;

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAllByType($msg_type, $field = "_id")
    {
        $query = $this->getQuery();
        if (!empty($msg_type)) {
            $query['msg_type'] = $msg_type;
        }
        $sort = array('_id' => 1);
        $list = $this->findAll($query, $sort);

        $options = array();
        foreach ($list as $item) {
            $options[$item[$field]] = $item['name'];
        }
        return $options;
    }
}
