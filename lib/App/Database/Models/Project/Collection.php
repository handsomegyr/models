<?php

namespace App\Database\Models\Project;

class Collection extends \App\Common\Models\Database\Project\Collection
{

    /**
     * 检测一个集合是否存在，根据名称和编号
     *
     * @param string $name            
     * @return boolean
     */
    public function checkCollecionNameExist($name, $project_id, $company_project_id)
    {
        // 检查当前项目集合中是否包含这些命名
        $info = $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'name' => $name,
        ));
        if (empty($info)) {
            return false;
        }
        return true;
    }

    public function checkCollecionAliasExist($alias, $project_id, $company_project_id)
    {
        // 检查当前项目集合中是否包含这些命名
        $info = $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'alias' => $alias,
        ));
        if (empty($info)) {
            return false;
        }
        return true;
    }

    public function checkPluginNameExist($name, $project_id, $company_project_id)
    {
        // 检查插件集合中是否包含这些名称信息
        $info = $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'name' => $name,
            'plugin' => true
        ));

        if (empty($info)) {
            return false;
        }
        return true;
    }

    public function checkPluginAliasExist($alias, $project_id, $company_project_id)
    {
        // 检查插件集合中是否包含这些名称信息
        $info = $this->findOne(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'alias' => $alias,
            'plugin' => true
        ));

        if (empty($info)) {
            return false;
        }
        return true;
    }

    /**
     * 根据集合的名称获取集合的_id
     *
     * @param string $project_id            
     * @param string $alias            
     * @throws \Exception or string
     */
    public function getCollectionIdByAlias($project_id, $alias)
    {
        // if (isValidMongoIdString($alias)) {
        //     return $alias;
        // }
        // // try {
        // // new \MongoId($alias);
        // // return $alias;
        // // } catch (\MongoException $ex) {}

        $collectionInfo = $this->findOne(array(
            'project_id' => $project_id,
            'alias' => $alias
        ));

        if ($collectionInfo == null) {
            return false;
        } else {
            return $collectionInfo['_id'];
        }
    }
}
