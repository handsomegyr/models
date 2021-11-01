<?php

namespace App\Lexiangla\Models\Contact;

class TagUser extends \App\Common\Models\Lexiangla\Contact\TagUser
{
    /**
     * 根据用户ID和标签ID获取信息
     *
     * @param string $userid   
     * @param string $tagid         
     */
    public function getInfoByUseridAndTagid($userid, $tagid)
    {
        $query = array();
        $query['userid'] = trim($userid);
        $query['tagid'] = trim($tagid);

        $result = $this->findOne($query);
        return $result;
    }

    public function clearExist($tagid)
    {
        $updateData = array('is_exist' => 0);
        return $this->update(array('tagid' => $tagid), array('$set' => $updateData));
    }

    public function syncTagUserList($tagid, $res, $now)
    {
        $this->clearExist($tagid);
        /**
         * {
         *      "msg": "success",
         *      "code": 0,
         *      "data": {
         *          "department_list": [
         *          ],
         *          "name": "标签A",
         *          "id": "572c2858fe6f11ebb1d2a67595a2469f",
         *          "user_list": [
         *              "xiaoming",
         *              "zhangshan",
         *              "lisi"
         *          ]
         *      }
         *  }
         */
        if (!empty($res['data']['user_list'])) {
            foreach ($res['data']['user_list'] as $userid) {
                $username = "";
                $info = $this->getInfoByUseridAndTagid($userid, $tagid);
                $data = array();
                $data['tagname'] = isset($res['name']) ? $res['name'] : "";
                $data['username'] = $username;
                $data['is_exist'] = 1;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['memo'] = \App\Common\Utils\Helper::myJsonEncode($res);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['userid'] = $userid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
