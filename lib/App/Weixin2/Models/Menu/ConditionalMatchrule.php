<?php

namespace App\Weixin2\Models\Menu;

class ConditionalMatchrule extends \App\Common\Models\Weixin2\Menu\ConditionalMatchrule
{

    public function checkMatchRule($matchRule)
    {
        $ruleInfo = array();
        foreach (array(
            "tag_id",
            "sex",
            "country",
            "province",
            "city",
            "client_platform_type",
            "language"
        ) as $field) {
            $ruleInfo[$field] = empty($matchRule[$field]) ? '' : $matchRule[$field];
        }
        $plainText = implode('', $ruleInfo);
        if (empty($plainText)) {
            return false;
        }
        $ruleInfo['_id'] = $matchRule['_id'];
        return $ruleInfo;
    }
}
