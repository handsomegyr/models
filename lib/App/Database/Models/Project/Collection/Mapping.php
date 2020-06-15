<?php

namespace App\Database\Models\Project\Collection;

class Mapping extends \App\Common\Models\Database\Project\Collection\Mapping
{
    public function getMapping($collection_id, $project_id, $company_project_id)
    {
        $info = $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'collection_id' => $collection_id,
            'is_actived' => true
        ));
        return $info;
    }
}
