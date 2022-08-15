<?php

namespace App\Backend\Submodules\Qyweixin\Models\Attachment;

class Attachment extends \App\Common\Models\Qyweixin\Attachment\Attachment
{

    use \App\Backend\Models\Base;

    /**
     * 根据类型获取所有列表
     *
     * @return array
     */
    public function getAllByType($type, $field = "_id")
    {
        $query = $this->getQuery();
        if (!empty($type)) {
            $query['type'] = $type;
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
