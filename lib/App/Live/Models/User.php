<?php

namespace App\Live\Models;

class User extends \App\Common\Models\Live\User
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => -1
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

    public function getFrontData($info)
    {
        $data = array();
        $data['user_id'] = $info['_id'];
        $data['openid'] = $info['openid'];
        $data['nickname'] = $info['nickname'];
        $data['headimgurl'] = $info['headimgurl'];
        $data['worth'] = $info['worth'];
        $data['worth2'] = $info['worth2'];
        return $data;
    }

    public function getRedisData($info)
    {
        $data = array();
        $data['user_id'] = $info['_id'];
        $data['openid'] = $info['openid'];
        $data['nickname'] = $info['nickname'];
        $data['headimgurl'] = $info['headimgurl'];
        $data['worth'] = $info['worth'];
        $data['worth2'] = $info['worth2'];
        $data['authtype'] = empty($info['authtype']) ? '' : $info['authtype'];
        $data['redpack_user'] = empty($info['redpack_user']) ? '' : $info['redpack_user'];
        $data['thirdparty_user'] = empty($info['thirdparty_user']) ? '' : $info['thirdparty_user'];
        $data['is_auchor'] = empty($info['is_auchor']) ? 0 : 1;
        $data['auchor_id'] = $info['auchor_id'];
        $data['is_vip'] = empty($info['is_vip']) ? 0 : 1;
        $data['is_test'] = empty($info['is_test']) ? 0 : 1;
        return $data;
    }

    /**
     * 从redis中获取用户信息
     *
     * @param string $user_id            
     * @return array
     */
    public function getInfoFromRedis($user_id)
    {
        $user_info_json = $this->redis->hGet($this->getRedisKey(), $user_id);
        if (empty($user_info_json)) {
            return array();
        }
        $user_info_json = json_decode($user_info_json, true);
        if (empty($user_info_json)) {
            return array();
        }

        return $user_info_json;
    }

    /**
     * 记录用户信息到redis
     *
     * @param array $info            
     */
    public function saveInfoInRedis($info, $isForce = true)
    {
        // 记录用户信息到redis
        $info = $this->getRedisData($info);
        if ($isForce || !$this->redis->hexists($this->getRedisKey(), $info['user_id'])) {
            $this->redis->hSet($this->getRedisKey(), $info['user_id'], \App\Common\Utils\Helper::myJsonEncode($info));
        }
    }

    // 用户信息KEY
    protected function getRedisKey()
    {
        return $this->prefix . 'userinfolist';
    }

    /**
     * 根据微信号获取信息
     *
     * @param string $openid            
     * @param array $otherCondition            
     * @return array
     */
    public function getInfoByOpenid($openid, array $otherCondition = array())
    {
        $query = array(
            'openid' => $openid
        );
        if (!empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 生成记录
     *
     * @param string $openid            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param string $redpack_user            
     * @param string $thirdparty_user            
     * @param int $worth            
     * @param int $worth2            
     * @param string $room_id            
     * @param array $memo            
     * @return array
     */
    public function create($openid, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth = 0, $worth2 = 0, $room_id, $authtype, $source, $channel, $is_auchor = false, $is_vip = false, $is_test = false, array $memo = array('memo' => ''))
    {
        $data = array();
        $data['openid'] = $openid; // 微信ID
        $data['nickname'] = $nickname; // 昵称
        $data['headimgurl'] = $headimgurl; // 头像
        $data['redpack_user'] = $redpack_user; // 国泰微信ID
        $data['thirdparty_user'] = $thirdparty_user; // 第3方账号
        $data['worth'] = intval($worth); // 价值
        $data['worth2'] = intval($worth2); // 价值2

        $data['room_id'] = strval($room_id); // 直播房间
        $data['authtype'] = $authtype; // 登陆方式
        $data['source'] = $source; // 来源
        $data['channel'] = $channel; // 渠道

        $data['is_auchor'] = $is_auchor; // 是否是主播
        $data['is_vip'] = $is_vip; // 是否是VIP用户
        $data['is_test'] = $is_test; // 是否是测试用户

        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据userid生成或获取记录
     *
     * @param string $openid            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param string $redpack_user            
     * @param string $thirdparty_user            
     * @param int $worth            
     * @param int $worth2            
     * @param string $room_id            
     * @param array $memo            
     * @return array
     */
    public function getOrCreateByOpenid($openid, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth = 0, $worth2 = 0, $room_id, $authtype, $source, $channel, $is_auchor = false, $is_vip = false, $is_test = false, array $memo = array())
    {
        $info = $this->getInfoByOpenid($openid);
        if (empty($info)) {
            $info = $this->create($openid, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth, $worth2, $room_id, $authtype, $source, $channel, $is_auchor, $is_vip, $is_test, $memo);
        }
        return $info;
    }

    /**
     * 增加价值
     *
     * @param mixed $idOrObject            
     * @param int $worth            
     * @param int $worth2            
     * @param array $otherIncData            
     * @param array $otherUpdateData            
     * @throws Exception
     * @return boolean
     */
    public function incWorth($idOrObject, $worth = 0, $worth2 = 0, array $otherIncData = array(), array $otherUpdateData = array())
    {
        if (is_string($idOrObject)) {
            $id = $idOrObject;
        } else {
            $id = empty($idOrObject['_id']) ? '' : $idOrObject['_id'];
        }
        if (empty($id)) {
            throw new \Exception("记录id不存在");
        }

        $query = array(
            '_id' => $id
        );

        $updateData = array(
            '$inc' => array()
        );

        if (!empty($worth)) {
            $updateData['$inc']['worth'] = $worth;
        }
        if (!empty($worth2)) {
            $updateData['$inc']['worth2'] = $worth2;
        }
        if (!empty($otherIncData)) {
            $updateData['$inc'] = array_merge($updateData['$inc'], $otherIncData);
        }
        if (!empty($otherUpdateData)) {
            $updateData['$set'] = $otherUpdateData;
        }
        $affectRows = 0;
        if (!empty($updateData)) {
            $affectRows = $this->update($query, $updateData);
        }
        return $affectRows;
    }
}
