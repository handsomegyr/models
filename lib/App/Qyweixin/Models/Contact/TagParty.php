<?php

namespace App\Qyweixin\Models\Contact;

class TagParty extends \App\Common\Models\Qyweixin\Contact\TagParty
{
    /**
     * 根据部门ID和标签ID获取信息
     *
     * @param string $deptid   
     * @param string $tagid          
     * @param string $authorizer_appid          
     */
    public function getInfoByDeptidAndTagid($deptid, $tagid, $authorizer_appid)
    {
        $query = array();
        $query['deptid'] = $deptid;
        $query['tagid'] = $tagid;
        $query['authorizer_appid'] = $authorizer_appid;
        $info = $this->findOne($query);

        return $info;
    }

    public function clearExist($tagid, $authorizer_appid, $provider_appid)
    {
        $updateData = array('is_exist' => 0);
        return $this->update(
            array(
                'tagid' => $tagid,
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncTagDepartmentList($tagid, $authorizer_appid, $provider_appid, $res, $now)
    {
        $this->clearExist($tagid, $authorizer_appid, $provider_appid);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "tagname": "乒乓球协会",
         * "userlist": [
         * {
         *  "userid": "zhangsan",
         *  "name": "李四"
         * }
         * ],
         * "partylist": [2]
         * }
         */
        if (!empty($res['partylist'])) {
            foreach ($res['partylist'] as $deptid) {
                $info = $this->getInfoByDeptidAndTagid($deptid, $tagid, $authorizer_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['tagname'] = $res['tagname'];
                if (isset($res['invalidparty'])) {
                    $data['invalidparty'] = \App\Common\Utils\Helper::myJsonEncode($res['invalidparty']);
                } else {
                    $data['invalidparty'] = "[]";
                }
                $data['is_exist'] = 1;
                $data['get_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                $data['memo'] = \App\Common\Utils\Helper::myJsonEncode($res);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['deptid'] = $deptid;
                    $data['tagid'] = $tagid;
                    $this->insert($data);
                }
            }
        }
    }
}
