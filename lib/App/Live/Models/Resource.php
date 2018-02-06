<?php
namespace App\Live\Models;

class Resource extends \App\Common\Models\Live\Resource
{

    /**
     * 保存信息到redis中去
     *
     * @param array $info            
     */
    public function saveInfoToRedis($info)
    {
        $key = $this->getRedisKey($info['contentType']);
        $this->redis->sAdd($key, $info['content']);
    }

    /**
     * 获取随机资源
     *
     * @return string
     */
    public function getRandom($type)
    {
        $key = $this->getRedisKey($type);
        $content = $this->redis->sRandmember($key);
        return $content;
    }

    /**
     * 获取随机资源的数量
     *
     * @return int
     */
    public function getCount($type)
    {
        $key = $this->getRedisKey($type);
        return $this->redis->sCard($key);
    }

    /**
     * 从redis中删除
     *
     * @param array $info            
     */
    public function removeFromRedis($info, $isDeleteData = false)
    {
        if ($isDeleteData) {
            $this->remove(array(
                '_id' => $info['_id']
            ));
        }
        $key = $this->getRedisKey($info['contentType']);
        $this->redis->sRem($key, $info['content']);
    }

    /**
     * 从redis中删除
     *
     * @param array $info            
     */
    public function removeAllFromRedis($isDeleteData = false)
    {
        if ($isDeleteData) {
            $this->remove(array());
        }
        $key = $this->getRedisKey(1);
        $this->redis->del($key);
        $key = $this->getRedisKey(2);
        $this->redis->del($key);
        $key = $this->getRedisKey(3);
        $this->redis->del($key);
        $key = $this->getRedisKey(4);
        $this->redis->del($key);
    }

    /**
     * 从redis中删除
     *
     * @param array $info            
     */
    public function removeOneTypeFromRedis($type, $isDeleteData = false)
    {
        $type = intval($type);
        if ($isDeleteData) {
            $this->remove(array(
                'contentType' => $type
            ));
        }
        $key = $this->getRedisKey($type);
        $this->redis->del($key);
    }

    public function getRedisKey($type)
    {
        return $this->prefix . 'robotresourcelist' . $type;
    }
}