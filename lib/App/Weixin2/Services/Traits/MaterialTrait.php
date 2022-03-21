<?php

namespace App\Weixin2\Services\Traits;

trait MaterialTrait
{

    public function addMaterial($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }
        $description = array();
        if (!empty($materialInfo['title'])) {
            $description['title'] = $materialInfo['title'];
        }
        if (!empty($materialInfo['introduction'])) {
            $description['introduction'] = $materialInfo['introduction'];
        }
        $filePath = $modelMaterial->getPhysicalFilePath($materialInfo['media']);
        $res = $this->getWeixinObject()
            ->getMaterialManager()
            ->addMaterial($materialInfo['type'], $filePath, $description);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMaterial->recordMediaId($material_id, $res, time());
        return $res;
    }

    public function deleteMaterial($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }
        $media_id = $materialInfo['media_id'];

        $res = $this->getWeixinObject()
            ->getMaterialManager()
            ->delMaterial($media_id);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMaterial->removeMediaId($material_id, $res, time());

        return $res;
    }

    public function addNews($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }

        // 查找对应的永久图文素材
        $modelMaterialNews = new \App\Weixin2\Models\Material\News();
        $articles = $modelMaterialNews->getArticlesByMaterialId($material_id);

        if (empty($articles)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久图文素材不存在");
        }

        // 新增永久图文素材
        $res = $this->getWeixinObject()
            ->getMaterialManager()
            ->addNews($articles);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMaterial->recordMediaId($material_id, $res, time());
        return $res;
    }

    public function updateNews($material_id)
    {
        $modelMaterial = new \App\Weixin2\Models\Material\Material();
        $materialInfo = $modelMaterial->getInfoById($material_id);
        if (empty($materialInfo)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久素材不存在");
        }

        // 查找对应的永久图文素材
        $modelMaterialNews = new \App\Weixin2\Models\Material\News();
        $articles = $modelMaterialNews->getArticlesByMaterialId($material_id);

        if (empty($articles)) {
            throw new \Exception("永久素材记录ID:{$material_id}所对应的永久图文素材不存在");
        }

        $media_id = $materialInfo['media_id'];

        foreach ($articles as $index => $article) {
            // 修改增永久图文素材
            $res = $this->getWeixinObject()
                ->getMaterialManager()
                ->updateNews($media_id, $index, $article);
            if (!empty($res['errcode'])) {
                throw new \Exception($res['errmsg'], $res['errcode']);
            }
        }

        $modelMaterial->recordMediaId($material_id, $res, time());
        return $res;
    }
}
