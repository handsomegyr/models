<?php

namespace App\Activity\Models;

class User extends \App\Common\Models\Activity\User
{

    /**
     * 根据微信号获取信息
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param array $otherCondition            
     * @return array
     */
    public function getInfoByUserId($user_id, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => strval($activity_id)
        );
        if (!empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    public function getInfoByThirdpartyUser($thirdparty_user, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'thirdparty_user' => $thirdparty_user,
            'activity_id' => strval($activity_id)
        );
        if (!empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    public function getInfoByRedpackUser($redpack_user, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'redpack_user' => $redpack_user,
            'activity_id' => strval($activity_id)
        );
        if (!empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 锁住记录
     *
     * @param int $id            
     */
    public function lockUser($id)
    {
        $query = array(
            '_id' => $id,
            '__FOR_UPDATE__' => true
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 生成记录
     *
     * @param string $activity_id
     * @param string $user_id
     * @param number $log_time            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param string $redpack_user            
     * @param string $thirdparty_user            
     * @param number $worth            
     * @param number $worth2            
     * @param string $activity_id              
     * @param string $scene          
     * @param array $extendFields          
     * @param array $memo            
     * @return array
     */
    public function create($activity_id, $user_id, $log_time, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth = 0, $worth2 = 0, $scene = "", array $extendFields = array(), array $memo = array('memo' => ''))
    {
        $data = array();
        $data['activity_id'] = strval($activity_id); // 邀请活动
        $data['user_id'] = $user_id; // 微信ID
        $data['nickname'] = $nickname; // 昵称
        $data['headimgurl'] = $headimgurl; // 头像
        $data['redpack_user'] = $redpack_user; // 国泰微信ID
        $data['thirdparty_user'] = $thirdparty_user; // 第3方账号
        $data['worth'] = intval($worth); // 价值
        $data['worth2'] = intval($worth2); // 价值2
        $data['log_time'] = \App\Common\Utils\Helper::getCurrentTime($log_time);
        $data['scene'] = $scene; // 场景
        if (!empty($extendFields)) {
            foreach ($extendFields as $field => $value) {
                $data[$field] = $value;
            }
        }
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据userid生成或获取记录
     *
     * @param string $user_id            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param string $redpack_user            
     * @param string $thirdparty_user            
     * @param number $worth            
     * @param number $worth2            
     * @param string $activity_id               
     * @param string $scene            
     * @param array $extendFields           
     * @param array $memo            
     * @return array
     */
    public function getOrCreateByUserId($activity_id, $user_id, $log_time, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth = 0, $worth2 = 0, $scene = "", array $extendFields = array(), array $memo = array())
    {
        $info = $this->getInfoByUserid($user_id, $activity_id);
        if (empty($info)) {
            $info = $this->create($activity_id, $user_id, $log_time, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth, $worth2, $scene, $extendFields, $memo);
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
     * @throws \Exception
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
