<?php

namespace App\Weixin2\Services\Traits;

trait FreePublishTrait
{
    public function publishDraft($draftInfo)
    {
        $modelDraft = new \App\Weixin2\Models\Draft\Draft();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($draftInfo)) {
            $draft_id = $draftInfo;
            $draftInfo = $modelDraft->getInfoById($draft_id);
            if (empty($draftInfo)) {
                throw new \Exception("草稿记录ID:{$draft_id}所对应的草稿不存在");
            }
        }

        $draft_id = $draftInfo['_id'];
        $media_id = $draftInfo['media_id'];

        $res = $this->getWeixinObject()
            ->getFreePublishManager()
            ->submit($media_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelDraft->recordFreePublishResult($draft_id, $res, time());
        return $res;
    }

    public function getDraftPublishInfo($draftInfo)
    {
        $modelDraft = new \App\Weixin2\Models\Draft\Draft();
        $modelFreePublish = new \App\Weixin2\Models\FreePublish\FreePublish();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($draftInfo)) {
            $draft_id = $draftInfo;
            $draftInfo = $modelDraft->getInfoById($draft_id);
            if (empty($draftInfo)) {
                throw new \Exception("草稿记录ID:{$draft_id}所对应的草稿不存在");
            }
        }
        $draft_id = $draftInfo['_id'];
        $publish_id = $draftInfo['publish_id'];
        $res = $this->getWeixinObject()
            ->getFreePublishManager()
            ->get($publish_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelFreePublish->syncFreePublish($draftInfo['authorizer_appid'], $draftInfo['component_appid'], $res, time());
        return $res;
    }

    public function batchgetFreePublishArticles($offset)
    {
        $modelFreePublishArticle = new \App\Weixin2\Models\FreePublish\Article();
        $count = 20;
        $no_content = 0;
        $res = $this->getWeixinObject()
            ->getFreePublishManager()
            ->batchget($offset, $count, $no_content);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelFreePublishArticle->syncFreePublishArticles($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function getFreePublishArticleInfo($article_id)
    {
        $modelFreePublishArticle = new \App\Weixin2\Models\FreePublish\Article();
        $res = $this->getWeixinObject()
            ->getFreePublishManager()
            ->getarticle($article_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelFreePublishArticle->syncFreePublishArticle($article_id, $this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }
}
