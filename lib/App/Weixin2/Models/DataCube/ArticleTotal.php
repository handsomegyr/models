<?php

namespace App\Weixin2\Models\DataCube;

class ArticleTotal extends \App\Common\Models\Weixin2\DataCube\ArticleTotal
{

    /**
     * 根据数据的日期获取信息
     *
     * @param string $ref_date            
     * @param string $msgid            
     * @param string $stat_date            
     * @param string $authorizer_appid            
     * @param string $component_appid         
     */
    public function getInfoByRefDate($ref_date, $msgid, $stat_date, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'ref_date' => $ref_date,
            'msgid' => $msgid,
            'stat_date' => $stat_date,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncArticleTotal($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $l) {
                foreach ($l['details'] as $item) {
                    $ref_date = $l['ref_date'] . " 00:00:00";
                    $stat_date = $item['stat_date'] . " 00:00:00";
                    $info = $this->getInfoByRefDate($ref_date, $l['msgid'], $stat_date, $authorizer_appid, $component_appid);
                    $data = array();
                    /**
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
                     */

                    $data['title'] = $l['title'];

                    $data['target_user'] = $item['target_user'];
                    $data['int_page_read_user'] = $item['int_page_read_user'];
                    $data['int_page_read_count'] = $item['int_page_read_count'];
                    $data['ori_page_read_user'] = $item['ori_page_read_user'];
                    $data['ori_page_read_count'] = $item['ori_page_read_count'];

                    $data['share_user'] = $item['share_user'];
                    $data['share_count'] = $item['share_count'];
                    $data['add_to_fav_user'] = $item['add_to_fav_user'];
                    $data['add_to_fav_count'] = $item['add_to_fav_count'];

                    $data['int_page_from_session_read_user'] = $item['int_page_from_session_read_user'];
                    $data['int_page_from_session_read_count'] = $item['int_page_from_session_read_count'];

                    $data['int_page_from_hist_msg_read_user'] = $item['int_page_from_hist_msg_read_user'];
                    $data['int_page_from_hist_msg_read_count'] = $item['int_page_from_hist_msg_read_count'];

                    $data['int_page_from_feed_read_user'] = $item['int_page_from_feed_read_user'];
                    $data['int_page_from_feed_read_count'] = $item['int_page_from_feed_read_count'];

                    $data['int_page_from_friends_read_user'] = $item['int_page_from_friends_read_user'];
                    $data['int_page_from_friends_read_count'] = $item['int_page_from_friends_read_count'];

                    $data['int_page_from_other_read_user'] = $item['int_page_from_other_read_user'];
                    $data['int_page_from_other_read_count'] = $item['int_page_from_other_read_count'];

                    $data['int_page_from_kanyikan_read_user'] = $item['int_page_from_kanyikan_read_user'];
                    $data['int_page_from_kanyikan_read_count'] = $item['int_page_from_kanyikan_read_count'];

                    $data['int_page_from_souyisou_read_user'] = $item['int_page_from_souyisou_read_user'];
                    $data['int_page_from_souyisou_read_count'] = $item['int_page_from_souyisou_read_count'];

                    $data['feed_share_from_session_user'] = $item['feed_share_from_session_user'];
                    $data['feed_share_from_session_cnt'] = $item['feed_share_from_session_cnt'];

                    $data['feed_share_from_feed_user'] = $item['feed_share_from_feed_user'];
                    $data['feed_share_from_feed_cnt'] = $item['feed_share_from_feed_cnt'];

                    $data['feed_share_from_other_user'] = $item['feed_share_from_other_user'];
                    $data['feed_share_from_other_cnt'] = $item['feed_share_from_other_cnt'];

                    if (!empty($info)) {
                        $this->update(array('_id' => $info['_id']), array('$set' => $data));
                    } else {
                        $data['authorizer_appid'] = $authorizer_appid;
                        $data['component_appid'] = $component_appid;
                        $data['ref_date'] = $ref_date;
                        $data['msgid'] = $l['msgid'];
                        $data['stat_date'] = $stat_date;
                        $this->insert($data);
                    }
                }
            }
        }
    }
}
