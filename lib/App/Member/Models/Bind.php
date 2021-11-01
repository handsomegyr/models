<?php

namespace App\Member\Models;

class Bind extends \App\Common\Models\Member\Bind
{
    /**
     * 根据微信号获取信息
     *
     * @param string $openid 
     * @param string $mobile           
     * @return array
     */
    public function getInfoByOpenId($openid, $mobile = "")
    {
        $query = array();
        $query['openid'] = trim($openid);
        if (!empty($mobile)) {
            $query['mobile'] = trim($mobile);
        }
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile
     * @param string $openid             
     * @return array
     */
    public function getInfoByMobile($mobile, $openid = "")
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        if (!empty($mobile)) {
            $query['openid'] = trim($openid);
        }
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 根据手机号获取信息
     *
     * @param string $mobile            
     * @return array
     */
    public function getInfoByOtherOpenid($mobile, $openid)
    {
        $query = array();
        $query['mobile'] = trim($mobile);
        if (!empty($mobile)) {
            $query['openid'] = array('$ne' => trim($openid));
        }
        $result = $this->findOne($query);
        return $result;
    }

    /**
     * 生成记录
     *
     * @return array
     */
    public function logBind($mobile, $openid)
    {
        $data = array();
        $data['mobile'] = trim($mobile);
        $data['openid'] = trim($openid);
        $info = $this->insert($data);
        return $info;
    }
}
