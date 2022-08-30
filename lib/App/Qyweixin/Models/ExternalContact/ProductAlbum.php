<?php

namespace App\Qyweixin\Models\ExternalContact;

class ProductAlbum extends \App\Common\Models\Qyweixin\ExternalContact\ProductAlbum
{
    /**
     * 根据product_id获取信息
     *
     * @param string $product_id
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByProductId($product_id, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['product_id'] = $product_id;
        $query['agentid'] = $agentid;
        $query['authorizer_appid'] = $authorizer_appid;
        $query['provider_appid'] = $provider_appid;
        $info = $this->findOne($query);
        return $info;
    }

    public function clearExist($agentid, $authorizer_appid, $provider_appid, $now)
    {
        $updateData = array('is_exist' => 0, 'sync_time' => \App\Common\Utils\Helper::getCurrentTime($now));
        return $this->update(
            array(
                'agentid' => $agentid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncProductList($agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        $this->clearExist($agentid, $authorizer_appid, $provider_appid, $now);
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "next_cursor":"CURSOR",
        //     "product_list":[
        //         {
        //             "product_id" : "xxxxxxxxxx",
        //             "description":"世界上最好的商品",
        //             "price":30000,
        //             "product_sn":"xxxxxxxx",
        //             "attachments":[
        //                 {
        //                     "type": "image",
        //                     "image": {
        //                         "media_id": "MEDIA_ID"
        //                     }
        //                 }
        //             ]
        //         }
        //     ]
        // }

        if (!empty($res['product_list'])) {
            foreach ($res['product_list'] as $info) {
                $product_id = $info['product_id'];
                $info = $this->getInfoByProductId($product_id, $agentid, $authorizer_appid, $provider_appid);
                $data = array();
                $data['description'] = $info['description'];
                $data['product_sn'] = $info['product_sn'];
                $data['price'] = $info['price'];
                $data['attachments'] = empty($info['attachments']) ? "{}" : \App\Common\Utils\Helper::myJsonEncode($info['attachments']);
                $data['is_exist'] = 1;
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['agentid'] = $agentid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['provider_appid'] = $provider_appid;
                    $data['product_id'] = $product_id;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateProductId($info, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "product_id" : "xxxxxxxxxx"
        // }
        $data = array();
        $data['product_id'] = $res['product_id'];
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $info['_id']), array('$set' => $data));
    }

    public function updateProductInfo($info, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "product": {
        //             "product_id" : "xxxxxxxxxx",
        //             "description":"世界上最好的商品",
        //             "price":30000,
        //             "create_time":1600000000,
        //             "product_sn":"xxxxxxxx",
        //             "attachments":[
        //                 {
        //                     "type": "image",
        //                     "image": {
        //                         "media_id": "MEDIA_ID"
        //                     }
        //                 }
        //             ]
        //     }
        // }
        $data = array();
        $data['product_id'] = $res['product']['product_id'];
        $data['description'] = $res['product']['description'];
        $data['price'] = $res['product']['price'];
        $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($res['product']['create_time']);
        $data['product_sn'] = $res['product']['product_sn'];
        if (isset($res['product']['attachments'])) {
            $data['attachments'] = \App\Common\Utils\Helper::myJsonEncode($res['product']['attachments']);
        }
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $info['_id']), array('$set' => $data));
    }
}
