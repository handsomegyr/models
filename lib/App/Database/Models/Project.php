<?php

namespace App\Database\Models;

class Project extends \App\Common\Models\Database\Project
{
    /**
     * 检测一个项目是否存在，根据名称和编号
     *
     * @param string $name            
     * @return boolean
     */
    public function checkProjectNameExist($company_project_id, $name)
    {
        $info = $this->_project->findOne(array(
            'company_project_id' => $company_project_id,
            'name' => $name
        ));

        if (empty($info)) {
            return false;
        }
        return true;
    }

    public function checkProjectSnExist($company_project_id, $sn)
    {
        $info = $this->_project->findOne(array(
            'company_project_id' => $company_project_id,
            'sn' => $sn
        ));

        if (empty($info)) {
            return false;
        }
        return true;
    }
}
