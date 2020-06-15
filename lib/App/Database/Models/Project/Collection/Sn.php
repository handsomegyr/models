<?php

namespace App\Database\Models\Project\Collection;

class Sn extends \App\Common\Models\Database\Project\Collection\Sn
{
    public function getIsLock($collection_id, $project_id, $company_project_id)
    {
        $locked = false;
        $lockInfo = $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'collection_id' => $collection_id,
            'is_actived' => true
        ));
        if (!empty($lockInfo)) {
            $locked = true;
        }
        return $locked;
    }
}
