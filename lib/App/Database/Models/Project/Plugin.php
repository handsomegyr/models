<?php

namespace App\Database\Models\Project;

class Plugin extends \App\Common\Models\Database\Project\Plugin
{

    public function getInfoByPluginId($plugin_id, $project_id, $company_project_id)
    {
        return $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'plugin_id' => $plugin_id,
        ));
    }
}
