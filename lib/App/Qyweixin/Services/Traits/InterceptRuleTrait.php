<?php

namespace App\Qyweixin\Services\Traits;

trait InterceptRuleTrait
{
    /**
     * 获取敏感词规则管理器
     *
     * @return \Qyweixin\Manager\ExternalContact\InterceptRule
     */
    public function getInterceptRuleManager()
    {
        return $this->getQyWeixinObject()->getExternalContactManager()->getInterceptRuleManager();
    }

    // 获取敏感词规则列表
    public function getInterceptRuleList()
    {
        $modelInterceptRule = new \App\Qyweixin\Models\ExternalContact\InterceptRule();
        $res = $this->getInterceptRuleManager()->getList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
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
        $modelInterceptRule->syncRuleList($this->agentid, $this->authorizer_appid, $this->provider_appid, $res, time());
        return $res;
    }

    // 获取敏感词规则
    public function getInterceptRuleInfo($interceptRuleInfo)
    {
        $modelInterceptRule = new \App\Qyweixin\Models\ExternalContact\InterceptRule();
        $rule_id = $interceptRuleInfo['rule_id'];
        $res = $this->getInterceptRuleManager()->get($rule_id);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
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
        $modelInterceptRule->updateRuleInfo($interceptRuleInfo, $res, time());
        return $res;
    }

    // 创建敏感词规则
    public function addInterceptRule($interceptRuleInfo)
    {
        $modelInterceptRule = new \App\Qyweixin\Models\ExternalContact\InterceptRule();
        $objInterceptRule = new \Qyweixin\Model\ExternalContact\InterceptRule();
        $objInterceptRule->rule_name = $interceptRuleInfo['rule_name'];
        $objInterceptRule->word_list = \json_decode($interceptRuleInfo['word_list'], true);
        $objInterceptRule->semantics_list = \json_decode($interceptRuleInfo['semantics_list'], true);
        $objInterceptRule->intercept_type = $interceptRuleInfo['intercept_type'];
        $applicable_range_settings = \json_decode($interceptRuleInfo['applicable_range'], true);

        $applicable_range = new \Qyweixin\Model\ExternalContact\ApplicableRange();
        if (!empty($applicable_range_settings['user_list'])) {
            $applicable_range->user_list = $applicable_range_settings['user_list'];
        }
        if (!empty($applicable_range_settings['department_list'])) {
            $applicable_range->department_list = $applicable_range_settings['department_list'];
        }
        $objInterceptRule->applicable_range = $applicable_range;

        $res = $this->getInterceptRuleManager()->add($objInterceptRule);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //     "errcode":0,
        //     "errmsg":"ok",
        //     "rule_id" : "xxx"
        // }
        $modelInterceptRule->updateRuleId($interceptRuleInfo, $res, time());
        return $res;
    }

    // 更新敏感词规则
    public function updateInterceptRule($interceptRuleInfo)
    {
        $modelInterceptRule = new \App\Qyweixin\Models\ExternalContact\InterceptRule();
        $objInterceptRule = new \Qyweixin\Model\ExternalContact\InterceptRule();
        $objInterceptRule->rule_id = $interceptRuleInfo['rule_id'];
        $objInterceptRule->rule_name = $interceptRuleInfo['rule_name'];
        $objInterceptRule->word_list = \json_decode($interceptRuleInfo['word_list'], true);
        $objInterceptRule->semantics_list = \json_decode($interceptRuleInfo['semantics_list'], true);
        $objInterceptRule->intercept_type = $interceptRuleInfo['intercept_type'];
        if (isset($interceptRuleInfo['add_applicable_range'])) {
            $add_applicable_range_settings = \json_decode($interceptRuleInfo['add_applicable_range'], true);
            $applicable_range = new \Qyweixin\Model\ExternalContact\ApplicableRange();
            if (!empty($add_applicable_range_settings['user_list'])) {
                $applicable_range->user_list = $add_applicable_range_settings['user_list'];
            }
            if (!empty($add_applicable_range_settings['department_list'])) {
                $applicable_range->department_list = $add_applicable_range_settings['department_list'];
            }
            $objInterceptRule->add_applicable_range = $applicable_range;
        }
        if (isset($interceptRuleInfo['remove_applicable_range'])) {
            $remove_applicable_range_settings = \json_decode($interceptRuleInfo['remove_applicable_range'], true);
            $applicable_range = new \Qyweixin\Model\ExternalContact\ApplicableRange();
            if (!empty($remove_applicable_range_settings['user_list'])) {
                $applicable_range->user_list = $remove_applicable_range_settings['user_list'];
            }
            if (!empty($remove_applicable_range_settings['department_list'])) {
                $applicable_range->department_list = $remove_applicable_range_settings['department_list'];
            }
            $objInterceptRule->remove_applicable_range = $applicable_range;
        }
        $res = $this->getInterceptRuleManager()->update($objInterceptRule);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        // {
        //     "errcode":0,
        //     "errmsg":"ok"
        // }
        return $res;
    }

    // 删除敏感词规则
    public function deleteInterceptRule($interceptRuleInfo)
    {
        $modelInterceptRule = new \App\Qyweixin\Models\ExternalContact\InterceptRule();
        $rule_id = $interceptRuleInfo['rule_id'];
        $res = $this->getInterceptRuleManager()->del($rule_id);
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
        $modelInterceptRule->update(array('_id' => $interceptRuleInfo['_id']), array('$set' => $data));
        return $res;
    }
}
