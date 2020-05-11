<?php

namespace App\Backend\Submodules\Company\Models;

class Project extends \App\Common\Models\Company\Project
{

    use \App\Backend\Models\Base;

    public function checkProjectCode($id, $project_code)
    {
        /* 判断是否已经存在 */
        $query = array();
        $query['project_code'] = trim($project_code);
        if (!empty($id)) {
            $query['_id'] = array(
                '$ne' => $id
            );
        }
        $info = $this->findOne($query);
        if (!empty($info)) {
            throw new \Exception(sprintf("项目编号已存在", stripslashes($project_code)), 1);
        }
    }

    public function checkOperationCode($id, $operation_code)
    {
        /* 判断是否已经存在 */
        $query = array();
        $query['operation_code'] = trim($operation_code);
        if (!empty($id)) {
            $query['_id'] = array(
                '$ne' => $id
            );
        }
        $info = $this->findOne($query);
        if (!empty($info)) {
            throw new \Exception(sprintf("项目运维编号已存在", stripslashes($operation_code)), 1);
        }
    }
}
