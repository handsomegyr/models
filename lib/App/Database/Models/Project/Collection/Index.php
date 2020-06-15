<?php

namespace App\Database\Models\Project\Collection;

class Index extends \App\Common\Models\Database\Project\Collection\Index
{
    /**
     * 自动给给定集合创建索引
     *
     * @param string $collection_id            
     *
     */
    public function autoCreateIndexes($collection_id)
    {
        // $cursor = $this->find(array(
        //     'collection_id' => $collection_id
        // ));

        // $dataCollection = new Data($this->config);
        // $dataCollection->setCollection(iCollectionName($collection_id));
        // while ($cursor->hasNext()) {
        //     $index = $cursor->getNext();
        //     $keys = \json_decode($index['keys'], true);
        //     $dataCollection->ensureIndex($keys, array(
        //         'background' => true
        //     ));
        // }

        return true;
    }

    /**
     * 根据集合名称获取集合的索引数量
     *
     * @param string $company_project_id
     * @param string $project_id
     * @param string $collection_id
     * @return int
     */
    public function getIndexNumber($company_project_id, $project_id, $collection_id)
    {
        return $this->count(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'collection_id' => $collection_id
        ));
    }
}
