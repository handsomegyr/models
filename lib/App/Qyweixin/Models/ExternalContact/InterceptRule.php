<?php

namespace App\Qyweixin\Models\ExternalContact;

class InterceptRule extends \App\Common\Models\Qyweixin\ExternalContact\InterceptRule
{
    /**
     * 根据rule_id获取信息
     *
     * @param string $rule_id
     * @param string $agentid
     * @param string $authorizer_appid
     * @param string $provider_appid
     */
    public function getInfoByRuleId($rule_id, $agentid, $authorizer_appid, $provider_appid)
    {
        $query = array();
        $query['rule_id'] = $rule_id;
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
                'authorizer_appid' => $authorizer_appid,
                'provider_appid' => $provider_appid
            ),
            array('$set' => $updateData)
        );
    }

    public function syncRuleList($agentid, $authorizer_appid, $provider_appid, $res, $now)
    {
        $this->clearExist($agentid, $authorizer_appid, $provider_appid, $now);
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "rule_list":[
        //         {
        //             "rule_id":"xxxx",
        //             "rule_name":"rulename",
        //             "create_time":1600000000
        //         }
        //     ]
        // }

        if (!empty($res['rule_list'])) {
            foreach ($res['rule_list'] as $info) {
                $rule_id = $info['rule_id'];
                $info = $this->getInfoByRuleId($rule_id, $agentid, $authorizer_appid, $provider_appid);
                $data = array();
                $data['provider_appid'] = $provider_appid;
                $data['rule_name'] = $info['rule_name'];
                $data['create_time'] = \App\Common\Utils\Helper::getCurrentTime($info['create_time']);
                $data['is_exist'] = 1;
                $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
                if (!empty($info)) {
                    $this->update(array('_id' => $info['_id']), array('$set' => $data));
                } else {
                    // $data['agentid'] = $agentid;
                    $data['authorizer_appid'] = $authorizer_appid;
                    $data['rule_id'] = $rule_id;
                    $this->insert($data);
                }
            }
        }
    }

    public function updateRuleId($info, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "rule_id" : "xxxxxxxxxx"
        // }
        $data = array();
        $data['rule_id'] = $res['rule_id'];
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $info['_id']), array('$set' => $data));
    }

    public function updateRuleInfo($info, $res, $now)
    {
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "rule":{
        //         "rule_id":1,
        //         "rule_name":"rulename",
        //         "word_list":[
        //           "敏感词1","敏感词2"
        //         ],
        //         "extra_rule":{
        //             "semantics_list":[1,2,3],
        //         },
        //         "intercept_type":1,
        //         "applicable_range":{
        //             "user_list":["zhangshan"],
        //             "department_list":[2,3]
        //         }
        //     }        
        // }
        $data = array();
        $data['rule_id'] = $res['rule']['rule_id'];
        $data['rule_name'] = $res['rule']['rule_name'];
        if (isset($res['rule']['word_list'])) {
            $data['word_list'] = \App\Common\Utils\Helper::myJsonEncode($res['rule']['word_list']);
        }
        if (isset($res['rule']['extra_rule'])) {
            $data['extra_rule'] = \App\Common\Utils\Helper::myJsonEncode($res['rule']['extra_rule']);
        }
        if (isset($res['rule']['applicable_range'])) {
            $data['applicable_range'] = \App\Common\Utils\Helper::myJsonEncode($res['rule']['applicable_range']);
        }
        $data['intercept_type'] = $res['rule']['intercept_type'];
        $data['is_exist'] = 1;
        $data['sync_time'] = \App\Common\Utils\Helper::getCurrentTime($now);
        return $this->update(array('_id' => $info['_id']), array('$set' => $data));
    }
}
