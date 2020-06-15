<?php

namespace App\Database\Models\Project\Collection;

class Orderby extends \App\Common\Models\Database\Project\Collection\Orderby
{
    /**
     * 获取当前集合的排列顺序
     *
     * @return array
     */
    public function getDefaultOrder($company_project_id, $project_id, $collection_id)
    {
        $list = $this->findAll(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'collection_id' => $collection_id
        ), array(
            'priority' => -1,
            '_id' => -1
        ));

        $order = array();
        foreach ($list as  $row) {
            $order[$row['field']] = $row['show_order'];
        }

        if (!isset($order['_id'])) {
            $order['_id'] = -1;
        }
        return $order;
    }
}
