<?php

namespace App\Member\Models;

class Tag extends \App\Common\Models\Member\Tag
{
    /**
     * 根据微信号获取信息
     *
     * @param string $openid
     * @param string $tag_id           
     * @return array
     */
    public function getInfoByOpenId($openid, $tag_id)
    {
        $result = $this->findOne(array(
            "openid" => trim($openid),
            "tag_id" => trim($tag_id)
        ));
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param string $tag_id            
     * @return array
     */
    public function getInfoByMobile($mobile, $tag_id)
    {
        $result = $this->findOne(array(
            "mobile" => trim($mobile),
            "tag_id" => trim($tag_id)
        ));
        return $result;
    }

    /**
     * 根据memberid获取信息
     *
     * @param string $member_id
     * @param string $tag_id            
     * @return array
     */
    public function getInfoByMemberId($member_id, $tag_id)
    {
        $result = $this->findOne(array(
            "member_id" => trim($member_id),
            "tag_id" => trim($tag_id)
        ));
        return $result;
    }

    /**
     * 根据微信号获取列表
     *
     * @param string $openid
     * @param array $tag_id_list           
     * @return array
     */
    public function getListByOpenId($openid, $tag_id_list = array())
    {
        $query = array();
        $query['openid'] = trim($openid);

        if ($tag_id_list) {
            $query['tag_id'] = array(
                '$in' => $tag_id_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据手机号获取列表
     *
     * @param string $mobile
     * @param array $tag_id_list           
     * @return array
     */
    public function getListByMobile($mobile, $tag_id_list = array())
    {
        $query = array();
        $query['mobile'] = trim($mobile);

        if ($tag_id_list) {
            $query['tag_id'] = array(
                '$in' => $tag_id_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    /**
     * 根据memberid获取列表
     *
     * @param string $member_id
     * @param array $tag_id_list           
     * @return array
     */
    public function getListByMemberId($member_id, $tag_id_list = array())
    {
        $query = array();
        $query['member_id'] = trim($member_id);

        if ($tag_id_list) {
            $query['tag_id'] = array(
                '$in' => $tag_id_list
            );
        }

        $list = $this->findAll($query);
        return $list;
    }

    public function getMaxTagidListByMobile(
        $mobile,
        array $tag_category_list = array()
    ) {
        $query = array();
        $query['mobile'] = trim($mobile);

        if ($tag_category_list) {
            $query['category'] = array(
                '$in' => $tag_category_list
            );
        }
        $sort = array();
        $sort['num'] = -1;
        $list = $this->find($query, $sort, 0, 3);

        $max_num = 0;
        $info = array();
        if (!empty($list['datas'])) {
            foreach ($list['datas'] as $item) {
                if ($max_num < $item['num']) {
                    $max_num = $item['num'];
                    $info = $item;
                } elseif ($max_num > 0 && $max_num == $item['num']) {
                    $rand = mt_rand(0, 1);
                    if ($rand == 1) {
                        $info = $item;
                    }
                }
            }
        }
        return $info;
    }

    /**
     * 做统计记录
     *
     * @return array
     */
    public function logStat2(
        $tag_id,
        $member_id,
        $mobile,
        $openid,
        $act_num,
        $now
    ) {
        $act_num = intval($act_num);
        $info = $this->getInfoByMobile($mobile, $tag_id);
        if (empty($info)) {
            $data = array();
            $data['tag_id'] = trim($tag_id);
            $data['member_id'] = trim($member_id);
            $data['mobile'] = trim($mobile);
            $data['openid'] = trim($openid);
            $data['num'] = max($act_num, 0);
            $info = $this->insert($data);
        } else {
            if ($act_num > 0) {
                $query = array(
                    '_id' => $info['_id']
                );
                $updateData = array(
                    '$inc' => array(
                        'num' => $act_num
                    )
                );
                $this->update($query, $updateData);
                // 再次获取
                $info = $this->getInfoById($info['_id']);
            } elseif ($act_num < 0) {
                $act_num = abs($act_num);
                $query = array(
                    '_id' => $info['_id'],
                    'num' => array(
                        '$gte' => $act_num
                    )
                );
                $updateData = array(
                    '$inc' => array(
                        'num' => -$act_num
                    )
                );
                $this->update($query, $updateData);
                // 再次获取
                $info = $this->getInfoById($info['_id']);
            }
        }
        return $info;
    }
}
