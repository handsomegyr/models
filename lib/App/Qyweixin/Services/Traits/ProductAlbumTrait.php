<?php

namespace App\Qyweixin\Services\Traits;

trait ProductAlbumTrait
{
    /**
     * 获取商品图册管理器
     *
     * @return \Qyweixin\Manager\ExternalContact\ProductAlbum
     */
    public function getProductAlbumManager()
    {
        return $this->getQyWeixinObject()->getExternalContactManager()->getProductAlbumManager();
    }

    // 获取商品图册列表
    public function getProductAlbumList($cursor = "", $limit = 100)
    {
        $modelProductAlbum = new \App\Qyweixin\Models\ExternalContact\ProductAlbum();
        $res = $this->getProductAlbumManager()->getList($cursor, $limit);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
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
        $modelProductAlbum->syncProductList($this->agentid, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取商品图册
    public function getProductAlbum($productAlbumInfo)
    {
        $modelProductAlbum = new \App\Qyweixin\Models\ExternalContact\ProductAlbum();
        $product_id = $productAlbumInfo['product_id'];
        $res = $this->getProductAlbumManager()->get($product_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
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
        $modelProductAlbum->updateProductInfo($productAlbumInfo, $res, time());
        return $res;
    }

    // 创建商品图册
    public function addProductAlbum($productAlbumInfo)
    {
        $modelProductAlbum = new \App\Qyweixin\Models\ExternalContact\ProductAlbum();
        $objProductAlbum = new \Qyweixin\Model\ExternalContact\ProductAlbum();
        $objProductAlbum->description = $productAlbumInfo['description'];
        $objProductAlbum->price = $productAlbumInfo['price'];
        $objProductAlbum->product_sn = $productAlbumInfo['product_sn'];
        $attachments = json_decode($productAlbumInfo['attachments'], true);
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if ($attachment['type'] == 'image') {
                    $objImage = new \Qyweixin\Model\ExternalContact\ProductAlbum\Attachment\Image($attachment['image']['media_id']);
                    $objProductAlbum->attachments[] = $objImage;
                }
            }
        }
        $res = $this->getProductAlbumManager()->add($objProductAlbum);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "product_id" : "xxxxxxxxxx"
        // }
        $modelProductAlbum->updateProductId($productAlbumInfo, $res, time());
        return $res;
    }

    // 更新商品图册
    public function updateProductAlbum($productAlbumInfo)
    {
        $modelProductAlbum = new \App\Qyweixin\Models\ExternalContact\ProductAlbum();
        $objProductAlbum = new \Qyweixin\Model\ExternalContact\ProductAlbum();
        $objProductAlbum->product_id = $productAlbumInfo['product_id'];
        $objProductAlbum->description = $productAlbumInfo['description'];
        $objProductAlbum->price = $productAlbumInfo['price'];
        $objProductAlbum->product_sn = $productAlbumInfo['product_sn'];
        $attachments = json_decode($productAlbumInfo['attachments'], true);
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if ($attachment['type'] == 'image') {
                    $objImage = new \Qyweixin\Model\ExternalContact\ProductAlbum\Attachment\Image($attachment['image']['media_id']);
                    $objProductAlbum->attachments[] = $objImage;
                }
            }
        }
        $res = $this->getProductAlbumManager()->update($objProductAlbum);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        // }
        return $res;
    }

    // 删除商品图册
    public function deleteProductAlbum($productAlbumInfo)
    {
        $modelProductAlbum = new \App\Qyweixin\Models\ExternalContact\ProductAlbum();
        $product_id = $productAlbumInfo['product_id'];
        $res = $this->getProductAlbumManager()->delete($product_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //     "errcode":0,
        //     "errmsg":"ok"
        // }        
        $data = array();
        $data['is_exist'] = 0;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime(time());
        $modelProductAlbum->update(array('_id' => $productAlbumInfo['_id']), array('$set' => $data));
        return $res;
    }
}
