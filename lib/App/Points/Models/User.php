<?php

namespace App\Points\Models;

class User extends \App\Common\Models\Points\User
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'code' => 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 根据用户ID获取用户信息
     *
     * @param string $user_id            
     * @param number $category            
     * @return array
     */
    public function getInfoByUserId($user_id, $category)
    {
        $info = $this->findOne(array(
            'user_id' => strval($user_id),
            'category' => intval($category)
        ));
        return $info;
    }

    /**
     * 锁住记录
     *
     * @param int $id            
     */
    public function lockUser($id)
    {
        $user = $this->findOne(array(
            '_id' => $id,
            '__FOR_UPDATE__' => true
        ));
        return $user;
    }

    /**
     * 根据用户IDs获取用户列表信息
     *
     * @param string $user_id            
     * @param number $category            
     * @return array
     */
    public function getListByUserIds(array $user_ids, $category)
    {
        $query = array(
            'user_id' => array(
                '$in' => $user_ids
            ),
            'category' => intval($category)
        );
        $sort = array(
            '_id' => -1
        );
        $ret = $this->findAll($query, $sort);
        $list = array();
        if (!empty($ret)) {
            foreach ($ret as $item) {
                $list[$item['user_id']] = $item;
            }
        }
        return $list;
    }

    /**
     * 添加或消耗积分
     *
     * @param number $id             
     * @param number $points        
     * @param number $now          
     */
    public function addOrReduce($id, $points, $now)
    {
        $query = array();
        $query['_id'] = $id;

        $updateData = array();
        $updateData['point_time'] = \App\Common\Utils\Helper::getCurrentTime($now);

        $points = intval($points);
        if ($points != 0) {
            // 增加积分处理
            if ($points > 0) {
                $points = abs($points);
                $incData = array(
                    'current' => $points,
                    'total' => $points
                );
            } else {
                $points = abs($points);
                $query['current'] = array('$gte' => $points);
                $incData = array(
                    'current' => -$points,
                    'consume' => $points
                );
            }
        }

        $affectRows = $this->update($query, array(
            '$set' => $updateData,
            '$inc' => $incData,
        ));

        if ($affectRows < 1) {
            throw new \Exception("积分更新失败");
        } else {
            // 重新获取
            $newInfo = $this->getInfoById($id);
            return $newInfo;
        }
    }


    /**
     * 生成记录
     *
     * @param number $category            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl              
     * @param number $now           
     * @param number $current            
     * @param number $freeze            
     * @param number $consume            
     * @param number $expire            
     * @param array $memo            
     * @return array
     */
    public function create($category, $user_id, $user_name, $user_headimgurl, $now, $current = 0, $freeze = 0, $consume = 0, $expire = 0, array $memo = array('memo' => ''))
    {
        $data = array();
        $data['category'] = $category; // 积分分类
        $data['user_id'] = $user_id; // 微信ID
        $data['user_name'] = $user_name; // 用户名
        $data['user_headimgurl'] = $user_headimgurl; // 头像
        $data['current'] = $current; // 积分
        $data['total'] = $current; // 总积分
        $data['freeze'] = $freeze; // 冻结积分
        $data['consume'] = $consume; // 消费积分
        $data['expire'] = $expire; // 过期积分
        $data['point_time'] = \App\Common\Utils\Helper::getCurrentTime($now); // 积分时间
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }

    public function updateUserInfo($info, $nickname, $headimgurl)
    {
        $updateData = array();
        if (!empty($nickname) && $info['user_name'] != $nickname) {
            $updateData['user_name'] = $nickname;
        }
        if (!empty($headimgurl) && $info['user_headimgurl'] != $headimgurl) {
            $updateData['user_headimgurl'] = $headimgurl;
        }
        if (!empty($updateData)) {
            $this->update(array('_id' => $info['_id']), array('$set' => $updateData));
            $info = array_merge($info, $updateData);
        }
        return $info;
    }
}
