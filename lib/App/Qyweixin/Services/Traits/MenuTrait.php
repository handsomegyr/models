<?php

namespace App\Qyweixin\Services\Traits;

trait MenuTrait
{
    public function createMenu()
    {
        $modelMenu = new \App\Qyweixin\Models\Menu\Menu();
        $menus = $modelMenu->buildMenu($this->authorizer_appid, $this->provider_appid, $this->agentid);
        // return $menus;
        $res = $this->getQyWeixinObject()
            ->getMenuManager()
            ->create($this->agentid, $menus);

        return $res;
    }
}
