<?php

namespace App\Lexiangla\Models\Contact;

class Tag extends \App\Common\Models\Lexiangla\Contact\Tag
{
    /**
     * 根据标签ID获取信息
     *
     * @param string $tagid          
     */
    public function getInfoByTagId($tagid)
    {
        $query = array();
        $query['tagid'] = trim($tagid);

        $result = $this->findOne($query);
        return $result;
    }

    public function clearExist()
    {
        $updateData = array('is_exist' => false);
        return $this->update(array('_id' => array('$gt' => '')), array('$set' => $updateData));
    }

    public function syncTagList($res, $now)
    {
        $this->clearExist();
        /**
         * {
         *      "msg": "success",
         *      "code": 0,
         *      "data": {
         *          "total": 2,
         *          "list": [
         *              {
         *                  "name": "标签A",
         *                  "id": "572c2858fe6f11ebb1d2a67595a2469f"
         *              },
         *              {
         *                  "name": "标签B",
         *                  "id": "954b52c0fe7011eb9f34d229e73ba5ca"
         *              }
         *          ]
         *      }
         *  }
         */
        if (!empty($res['data']['list'])) {
            foreach ($res['data']['list'] as $tagInfo) {
                $tagid = $tagInfo['id'];
                $info = $this->getInfoByTagId($tagid);
                $data = array();
                $data['tagname'] = $tagInfo['name'];
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                // 通过这个字段来表明乐享那边有这条记录
                $data['is_exist'] = 1;
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }

    // 按乐享标签名获取乐享标签的信息
    public function getInfoByTagName($tagname)
    {
        // 查找乐享标签的表记录
        $query = array();
        $query['tagname'] = trim($tagname);
        $query['is_exist'] = true;

        $result = $this->findOne($query);
        return $result;
    }

    // 如果标签名称或顺序发生了改变的话
    public function isTagInfoChanged($qyweixinTagInfo, $lexiangTagInfo)
    {
        if (($lexiangTagInfo['tagname'] != $qyweixinTagInfo['tagname'])) {
            return true;
        }
        return false;
    }
}
