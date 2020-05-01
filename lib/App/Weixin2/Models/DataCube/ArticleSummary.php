<?php

namespace App\Weixin2\Models\DataCube;


class ArticleSummary extends \App\Common\Models\Weixin2\DataCube\ArticleSummary
{

    /**
     * 根据数据的日期获取信息
     *
     * @param string $ref_date            
     * @param string $msgid            
     * @param string $authorizer_appid            
     * @param string $component_appid           
     */
    public function getInfoByRefDate($ref_date, $msgid, $authorizer_appid, $component_appid)
    {
        $info = $this->findOne(array(
            'ref_date' => $ref_date,
            'msgid' => $msgid,
            'authorizer_appid' => $authorizer_appid,
            'component_appid' => $component_appid
        ));
        return $info;
    }

    public function syncArticleSummary($authorizer_appid, $component_appid, $res, $now)
    {
        if (!empty($res['list'])) {
            foreach ($res['list'] as $item) {
                $ref_date = $item['ref_date'] . " 00:00:00";
                $info = $this->getInfoByRefDate($ref_date, $item['msgid'], $authorizer_appid, $component_appid);
                $data = array();
                /**
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
                 */
                $data['title'] = $item['title'];
                $data['int_page_read_user'] = $item['int_page_read_user'];
                $data['int_page_read_count'] = $item['int_page_read_count'];
                $data['ori_page_read_user'] = $item['ori_page_read_user'];
                $data['ori_page_read_count'] = $item['ori_page_read_count'];

                $data['share_user'] = $item['share_user'];
                $data['share_count'] = $item['share_count'];
                $data['add_to_fav_user'] = $item['add_to_fav_user'];
                $data['add_to_fav_count'] = $item['add_to_fav_count'];

                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['component_appid'] = $component_appid;
                    $data['ref_date'] = $ref_date;
                    $data['msgid'] = $item['msgid'];
                    $this->insert($data);
                }
            }
        }
    }
}
