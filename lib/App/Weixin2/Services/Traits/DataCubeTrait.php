<?php

namespace App\Weixin2\Services\Traits;

trait DataCubeTrait
{

    public function syncUserSummary($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserSummary = new \App\Weixin2\Models\DataCube\UserSummary();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserSummary($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "user_source": 0,
         * "new_user": 0,
         * "cancel_user": 0
         * }//后续还有ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserSummary->syncUserSummary($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserCumulate($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserCumulate = new \App\Weixin2\Models\DataCube\UserCumulate();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserCumulate($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "cumulate_user": 1217056
         * }, //后续还有ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserCumulate->syncUserCumulate($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncInterfaceSummary($start_ref_date, $end_ref_date)
    {
        $modelDataCubeInterfaceSummary = new \App\Weixin2\Models\DataCube\InterfaceSummary();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getInterfaceSummary($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "callback_count": 36974,
         * "fail_count": 67,
         * "total_time_cost": 14994291,
         * "max_time_cost": 5044
         * }//后续还有不同ref_date（在begin_date和end_date之间）的数据
         * ]
         * }
         */
        $modelDataCubeInterfaceSummary->syncInterfaceSummary($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncInterfaceSummaryHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeInterfaceSummaryHour = new \App\Weixin2\Models\DataCube\InterfaceSummaryHour();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getInterfaceSummaryHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-01",
         * "ref_hour": 0,
         * "callback_count": 331,
         * "fail_count": 18,
         * "total_time_cost": 167870,
         * "max_time_cost": 5042
         * }//后续还有不同ref_hour的数据
         * ]
         * }
         */
        $modelDataCubeInterfaceSummaryHour->syncInterfaceSummaryHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsg($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsg = new \App\Weixin2\Models\DataCube\UpstreamMsg();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsg($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "msg_type": 1,
         * "msg_user": 282,
         * "msg_count": 817
         * }//后续还有同一ref_date的不同msg_type的数据，以及不同ref_date（在时间范围内）的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsg->syncUpstreamMsg($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgHour = new \App\Weixin2\Models\DataCube\UpstreamMsgHour();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "ref_hour": 0,
         * "msg_type": 1,
         * "msg_user": 9,
         * "msg_count": 10
         * }//后续还有同一ref_hour的不同msg_type的数据，以及不同ref_hour的数据，ref_date固定，因为最大时间跨度为1
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgHour->syncUpstreamMsgHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgWeek($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgWeek = new \App\Weixin2\Models\DataCube\UpstreamMsgWeek();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgWeek($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-08",
         * "msg_type": 1,
         * "msg_user": 16,
         * "msg_count": 27
         * } //后续还有同一ref_date下不同msg_type的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgWeek->syncUpstreamMsgWeek($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgMonth($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgMonth = new \App\Weixin2\Models\DataCube\UpstreamMsgMonth();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgMonth($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-11-01",
         * "msg_type": 1,
         * "msg_user": 7989,
         * "msg_count": 42206
         * }//后续还有同一ref_date下不同msg_type的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgMonth->syncUpstreamMsgMonth($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDist($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDist = new \App\Weixin2\Models\DataCube\UpstreamMsgDist();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDist($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDist->syncUpstreamMsgDist($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDistHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDistHour = new \App\Weixin2\Models\DataCube\UpstreamMsgDistHour();

        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDistHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDistHour->syncUpstreamMsgDistHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDistWeek($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDistWeek = new \App\Weixin2\Models\DataCube\UpstreamMsgDistWeek();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDistWeek($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDistWeek->syncUpstreamMsgDistWeek($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUpstreamMsgDistMonth($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUpstreamMsgDistMonth = new \App\Weixin2\Models\DataCube\UpstreamMsgDistMonth();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUpstreamMsgDistMonth($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "count_interval": 1,
         * "msg_user": 246
         * }//后续还有同一ref_date下不同count_interval的数据，及不同ref_date的数据
         * ]
         * }
         */
        $modelDataCubeUpstreamMsgDistMonth->syncUpstreamMsgDistMonth($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncArticleSummary($start_ref_date, $end_ref_date)
    {
        $modelDataCubeArticleSummary = new \App\Weixin2\Models\DataCube\ArticleSummary();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getArticleSummary($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-08",
         * "msgid": "10000050_1",
         * "title": "12月27日 DiLi日报",
         * "int_page_read_user": 23676,
         * "int_page_read_count": 25615,
         * "ori_page_read_user": 29,
         * "ori_page_read_count": 34,
         * "share_user": 122,
         * "share_count": 994,
         * "add_to_fav_user": 1,
         * "add_to_fav_count": 3
         * }
         * //后续会列出该日期内所有被阅读过的文章（仅包括群发的文章）在当天的阅读次数等数据
         * ]
         * }
         */
        $modelDataCubeArticleSummary->syncArticleSummary($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncArticleTotal($start_ref_date, $end_ref_date)
    {
        $modelDataCubeArticleTotal = new \App\Weixin2\Models\DataCube\ArticleTotal();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);
        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getArticleTotal($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-14",
         * "msgid": "202457380_1",
         * "title": "马航丢画记",
         * "details": [
         * {
         * "stat_date": "2014-12-14",
         * "target_user": 261917,
         * "int_page_read_user": 23676,
         * "int_page_read_count": 25615,
         * "ori_page_read_user": 29,
         * "ori_page_read_count": 34,
         * "share_user": 122,
         * "share_count": 994,
         * "add_to_fav_user": 1,
         * "add_to_fav_count": 3,
         * "int_page_from_session_read_user": 657283,
         * "int_page_from_session_read_count": 753486,
         * "int_page_from_hist_msg_read_user": 1669,
         * "int_page_from_hist_msg_read_count": 1920,
         * "int_page_from_feed_read_user": 367308,
         * "int_page_from_feed_read_count": 433422,
         * "int_page_from_friends_read_user": 15428,
         * "int_page_from_friends_read_count": 19645,
         * "int_page_from_other_read_user": 477,
         * "int_page_from_other_read_count": 703,
         * "feed_share_from_session_user": 63925,
         * "feed_share_from_session_cnt": 66489,
         * "feed_share_from_feed_user": 18249,
         * "feed_share_from_feed_cnt": 19319,
         * "feed_share_from_other_user": 731,
         * "feed_share_from_other_cnt": 775
         * }, //后续还会列出所有stat_date符合“ref_date（群发的日期）到接口调用日期”（但最多只统计7天）的数据
         * ]
         * },//后续还有ref_date（群发的日期）在begin_date和end_date之间的群发文章的数据
         * ]
         * }
         */
        $modelDataCubeArticleTotal->syncArticleTotal($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserRead($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserRead = new \App\Weixin2\Models\DataCube\UserRead();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserRead($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "int_page_read_user": 45524,
         * "int_page_read_count": 48796,
         * "ori_page_read_user": 11,
         * "ori_page_read_count": 35,
         * "share_user": 11,
         * "share_count": 276,
         * "add_to_fav_user": 5,
         * "add_to_fav_count": 15
         * }, //后续还有ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserRead->syncUserRead($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserReadHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserReadHour = new \App\Weixin2\Models\DataCube\UserReadHour();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserReadHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * {
         * "list": [
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 0,
         * "int_page_read_user": 6391,
         * "int_page_read_count": 7836,
         * "ori_page_read_user": 375,
         * "ori_page_read_count": 440,
         * "share_user": 2,
         * "share_count": 2,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * },
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 1,
         * "int_page_read_user": 1,
         * "int_page_read_count": 1,
         * "ori_page_read_user": 0,
         * "ori_page_read_count": 0,
         * "share_user": 0,
         * "share_count": 0,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * },
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 2,
         * "int_page_read_user": 3,
         * "int_page_read_count": 3,
         * "ori_page_read_user": 0,
         * "ori_page_read_count": 0,
         * "share_user": 0,
         * "share_count": 0,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * },
         * {
         * "ref_date": "2015-07-14",
         * "ref_hour": 0,
         * "user_source": 4,
         * "int_page_read_user": 42,
         * "int_page_read_count": 100,
         * "ori_page_read_user": 0,
         * "ori_page_read_count": 0,
         * "share_user": 0,
         * "share_count": 0,
         * "add_to_fav_user": 0,
         * "add_to_fav_count": 0
         * }
         * //后续还有ref_hour逐渐增大,以列举1天24小时的数据
         * ]
         * }
         */
        $modelDataCubeUserReadHour->syncUserReadHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserShare($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserShare = new \App\Weixin2\Models\DataCube\UserShare();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserShare($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "share_scene": 1,
         * "share_count": 207,
         * "share_user": 11
         * },
         * {
         * "ref_date": "2014-12-07",
         * "share_scene": 5,
         * "share_count": 23,
         * "share_user": 11
         * }//后续还有不同share_scene（分享场景）的数据，以及ref_date在begin_date和end_date之间的数据
         * ]
         * }
         */
        $modelDataCubeUserShare->syncUserShare($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }

    public function syncUserShareHour($start_ref_date, $end_ref_date)
    {
        $modelDataCubeUserShareHour = new \App\Weixin2\Models\DataCube\UserShareHour();
        $begin_date = date("Y-m-d", $start_ref_date);
        $end_date = date("Y-m-d", $end_ref_date);

        $res = $this->getWeixinObject()
            ->getDatacubeManager()
            ->getUserShareHour($begin_date, $end_date);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        /**
         * {
         * "list": [
         * {
         * "ref_date": "2014-12-07",
         * "ref_hour": 1200,
         * "share_scene": 1,
         * "share_count": 72,
         * "share_user": 4
         * }//后续还有不同share_scene的数据，以及ref_hour逐渐增大的数据。由于最大时间跨度为1，所以ref_date此处固定
         * ]
         * }
         */
        $modelDataCubeUserShareHour->syncUserShareHour($this->authorizer_appid, $this->component_appid, $res, time());
        return $res;
    }
}
