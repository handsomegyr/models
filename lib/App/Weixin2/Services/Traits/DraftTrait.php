<?php

namespace App\Weixin2\Services\Traits;

trait DraftTrait
{
    public function addDraft($draftInfo)
    {
        $modelDraft = new \App\Weixin2\Models\Draft\Draft();
        $modelDraftNews = new \App\Weixin2\Models\Draft\News();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($draftInfo)) {
            $draft_id = $draftInfo;
            $draftInfo = $modelDraft->getInfoById($draft_id);
            if (empty($draftInfo)) {
                throw new \Exception("草稿记录ID:{$draft_id}所对应的草稿不存在");
            }
        }
        $draft_id = $draftInfo['_id'];
        $draftArticles = $modelDraftNews->getArticlesByDraftId($draft_id, $draftInfo['authorizer_appid'], $draftInfo['component_appid']);

        if (empty($draftArticles)) {
            return new \Exception("草稿图文为空");
        }
        $articles = array();
        foreach ($draftArticles as $item) {
            $article = new \Weixin\Model\Draft\Article();
            // title	是	标题
            $article->title = $item['title'];
            // author	否	作者
            if (!empty($item['author'])) {
                $article->author = $item['author'];
            }
            // digest	否	图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空。如果本字段为没有填写，则默认抓取正文前54个字。
            if (!empty($item['digest'])) {
                $article->digest = $item['digest'];
            }
            // content	是	图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源 "上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。
            $article->content = $item['content'];
            // content_source_url	否	图文消息的原文地址，即点击“阅读原文”后的URL
            if (!empty($item['content_source_url'])) {
                $article->content_source_url = $item['content_source_url'];
            }
            // thumb_media_id	是	图文消息的封面图片素材id（必须是永久MediaID）
            $article->thumb_media_id = $item['thumb_media_id'];
            // show_cover_pic	否	是否显示封面，0为false，即不显示，1为true，即显示(默认)
            $article->show_cover_pic = $item['show_cover_pic'];
            // need_open_comment	否	Uint32 是否打开评论，0不打开(默认)，1打开
            $article->need_open_comment = $item['need_open_comment'];
            // only_fans_can_comment	否	Uint32 是否粉丝才可评论，0所有人可评论(默认)，1粉丝才可评论
            $article->only_fans_can_comment = $item['only_fans_can_comment'];
            $articles[] = $article;
        }

        $res = $this->getWeixinObject()
            ->getDraftManager()
            ->add($articles);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }

        $now = time();
        $modelDraft->recordMediaId($draft_id, $res, $now);
        $modelDraftNews->recordMediaId($draft_id, $draftInfo['authorizer_appid'], $draftInfo['component_appid'], $res, $now);
        return $res;
    }

    public function deleteDraft($draftInfo)
    {
        $modelDraft = new \App\Weixin2\Models\Draft\Draft();
        $modelDraftNews = new \App\Weixin2\Models\Draft\News();
        //标量变量是指那些包含了 integer、float、string 或 boolean的变量
        if (is_scalar($draftInfo)) {
            $draft_id = $draftInfo;
            $draftInfo = $modelDraft->getInfoById($draft_id);
            if (empty($draftInfo)) {
                throw new \Exception("草稿记录ID:{$draft_id}所对应的草稿不存在");
            }
        }
        $media_id = $draftInfo['media_id'];
        $draft_id = $draftInfo['_id'];

        $res = $this->getWeixinObject()
            ->getDraftManager()
            ->delete($media_id);

        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $now = time();
        $modelDraft->removeMediaId($draft_id, $res, $now);
        $modelDraftNews->removeMediaId($draft_id, $draftInfo['authorizer_appid'], $draftInfo['component_appid']);

        return $res;
    }

    public function batchgetDraftNews($offset)
    {        
        $modelDraftNews = new \App\Weixin2\Models\Draft\News();
        $count = 20;
        $no_content = 0;
        $res = $this->getWeixinObject()
            ->getDraftManager()
            ->batchget($offset, $count, $no_content);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelDraftNews->syncDraftNews($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }
}
