<?php

namespace App\Weixin2\Services\Traits;

trait MenuTrait
{
    public function createMenu()
    {
        $modelMenu = new \App\Weixin2\Models\Menu\Menu();
        $menus = $modelMenu->buildMenu($this->authorizer_appid, $this->component_appid);
        // return $menus;
        $res = $this->getWeixinObject()
            ->getMenuManager()
            ->create($menus);

        return $res;
    }

    public function createConditionalMenu($matchrule_id)
    {
        $modelMenuConditionalMatchrule = new \App\Weixin2\Models\Menu\ConditionalMatchrule();
        $matchRule = $modelMenuConditionalMatchrule->getInfoById($matchrule_id);
        if (empty($matchRule)) {
            throw new \Exception("匹配规则记录ID:{$matchrule_id}所对应的匹配规则不存在");
        }
        // 检查匹配规则是否有效
        $ruleInfo = $modelMenuConditionalMatchrule->checkMatchRule($matchRule);
        if (empty($ruleInfo)) {
            throw new \Exception("规则名:{$matchRule['matchrule_name']}所对应的匹配规则设置不正确,请至少设置一项");
        }

        // 增加菜单
        $modelMenuConditional = new \App\Weixin2\Models\Menu\Conditional();
        $menusWithMatchrule = $modelMenuConditional->buildMenusWithMatchrule($ruleInfo, $this->authorizer_appid, $this->component_appid);
        // return $menusWithMatchrule;
        $res = $this->getWeixinObject()
            ->getMenuManager()
            ->addconditional($menusWithMatchrule);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMenuConditional->recordMenuId($matchrule_id, $res['menuid'], time());
        return $res;
    }

    public function deleteConditionalMenu($matchrule_id, $menuid)
    {
        $res = $this->getWeixinObject()
            ->getMenuManager()
            ->delconditional($menuid);
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $modelMenuConditional = new \App\Weixin2\Models\Menu\Conditional();
        $modelMenuConditional->removeMenuId($matchrule_id, $menuid);
        return $res;
    }
}
