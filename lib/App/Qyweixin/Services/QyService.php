<?php

namespace App\Qyweixin\Services;

class QyService
{
    use \App\Qyweixin\Services\Traits\AgentMsgsTrait;
    use \App\Qyweixin\Services\Traits\AppchatMsgTrait;
    use \App\Qyweixin\Services\Traits\ContactTrait;
    use \App\Qyweixin\Services\Traits\ExternalContactTrait;
    use \App\Qyweixin\Services\Traits\LinkedcorpMsgTrait;
    use \App\Qyweixin\Services\Traits\MediaTrait;
    use \App\Qyweixin\Services\Traits\MenuTrait;
    use \App\Qyweixin\Services\Traits\ReplyMsgTrait;
    use \App\Qyweixin\Services\Traits\TransferTrait;

    private $authorizer_appid = "";

    private $provider_appid = "";

    private $providerConfig = array();

    private $authorizerConfig = array();

    private $agentid = 0;

    /**
     * @var \Qyweixin\Client
     */
    private $objQyWeixin = null;

    /**
     *
     * @var \Qyweixin\Service
     */
    private $objQyWeixinProvider = null;

    /**
     * @var \App\Qyweixin\Models\Provider\Provider
     */
    private $modelQyweixinProvider;

    /**
     * @var \App\Qyweixin\Models\Authorize\Authorizer
     */
    private $modelQyweixinAuthorizer;

    /**
     * @var \App\Qyweixin\Models\Agent\Agent
     */
    private $modelQyweixinAgent;

    public function __construct($authorizer_appid, $provider_appid, $agentid)
    {
        $this->authorizer_appid = $authorizer_appid;
        $this->provider_appid = $provider_appid;
        $this->agentid = $agentid;
        $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinAgent = new \App\Qyweixin\Models\Agent\Agent();
    }

    public function getAuthorizerAppid()
    {
        return $this->authorizer_appid;
    }

    public function getProviderAppid()
    {
        return $this->provider_appid;
    }

    public function getAppConfig4Provider()
    {
        $this->getToken4Provider();
        return $this->providerConfig;
    }

    public function getQyweixinProvider()
    {
        $this->getToken4Provider();
        $this->objQyWeixinProvider = new \Qyweixin\Service();
        return $this->objQyWeixinProvider;
    }

    public function getAppConfig4Authorizer()
    {
        $this->getToken4Authorizer();
        return $this->authorizerConfig;
    }

    public function getQyWeixinObject()
    {
        if (empty($this->agentid)) {
            $this->getToken4Authorizer();
            $this->objQyWeixin = new \Qyweixin\Client($this->authorizerConfig['appid'], $this->authorizerConfig['appsecret']);
            if (!empty($this->authorizerConfig['access_token'])) {
                $this->objQyWeixin->setAccessToken($this->authorizerConfig['access_token']);
            }
        } else {
            $agentInfo = $this->modelQyweixinAgent->getTokenByAppid($this->provider_appid, $this->authorizer_appid, $this->agentid);
            $this->objQyWeixin = new \Qyweixin\Client($agentInfo['authorizer_appid'], $agentInfo['secret']);
            if (!empty($agentInfo['access_token'])) {
                $this->objQyWeixin->setAccessToken($agentInfo['access_token']);
            }
        }

        return $this->objQyWeixin;
    }

    protected function getToken4Provider()
    {
        if (empty($this->providerConfig)) {
            $this->providerConfig = $this->modelQyweixinProvider->getTokenByAppid($this->provider_appid);
            if (empty($this->providerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
            }
        }
    }

    protected function getToken4Authorizer()
    {
        if (empty($this->authorizerConfig)) {
            $this->authorizerConfig = $this->modelQyweixinAuthorizer->getInfoByAppid($this->provider_appid, $this->authorizer_appid, true);
            if (empty($this->authorizerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
        }
    }

    public function getAccessToken4Agent()
    {
        $agentInfo = $this->modelQyweixinAgent->getTokenByAppid($this->provider_appid, $this->authorizer_appid, $this->agentid);
        if (empty($agentInfo)) {
            throw new \Exception("对应的运用不存在");
        }
        return $agentInfo;
    }

    public function getAgentInfo()
    {
        $agentInfo = $this->getAccessToken4Agent();

        $res = $this->getQyWeixinObject()
            ->getAgentManager()
            ->get($this->agentid);
        /**
         * {
         * "errcode": 0,
         * "errmsg": "ok",
         * "agentid": 1000005,
         * "name": "HR助手",
         * "square_logo_url": "https://p.qlogo.cn/bizmail/FicwmI50icF8GH9ib7rUAYR5kicLTgP265naVFQKnleqSlRhiaBx7QA9u7Q/0",
         * "description": "HR服务与员工自助平台",
         * "allow_userinfos": {
         * "user": [
         * {"userid": "zhangshan"},
         * {"userid": "lisi"}
         * ]
         * },
         * "allow_partys": {
         * "partyid": [1]
         * },
         * "allow_tags": {
         * "tagid": [1,2,3]
         * },
         * "close": 0,
         * "redirect_domain": "open.work.weixin.qq.com",
         * "report_location_flag": 0,
         * "isreportenter": 0,
         * "home_url": "https://open.work.weixin.qq.com"
         * }
         */
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        $this->modelQyweixinAgent->updateAgentInfo($agentInfo['_id'], $res, time(), $agentInfo['memo']);
        return $res;
    }

    public function getAgentList()
    {
        $res = $this->getQyWeixinObject()
            ->getAgentManager()
            ->getAgentList();
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        return $res;
    }

    public function getAccessToken4Authorizer()
    {
        $modelAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $authorizerInfo = $modelAuthorizer->getTokenByAppid($this->provider_appid, $this->authorizer_appid);
        if (empty($authorizerInfo)) {
            throw new \Exception("对应的授权方不存在");
        }
        return $authorizerInfo;
    }

    //获取应用的jsapi_ticket
    public function getJsapiTicket4Agent()
    {
        $agentInfo = $this->getAccessToken4Agent();
        return $agentInfo['jsapi_ticket'];
    }

    //获取应用的JS-SDK使用权限签名
    public function getSignPackage($url)
    {
        $jsapi_ticket = $this->getJsapiTicket4Agent();
        $objJssdk = new \Qyweixin\Jssdk();
        return $objJssdk->getSignPackage($url, $jsapi_ticket);
    }

    // 获取应用的可见范围
    public function getLinkedcorpAgentPermList()
    {
        $res = $this->getQyWeixinObject()
            ->getLinkedcorpManager()
            ->getAgentManager()->getPermList();
        return $res;
        if (!empty($res['errcode'])) {
            throw new \Exception($res['errmsg'], $res['errcode']);
        }
        return $res;
    }

    private function replaceDescription($desc)
    {
        if (empty($desc) || !is_string($desc)) {
            return '';
        }
        $pattern = '/<img(.*)src(.*)=(.*)"(.*)"/U';
        preg_replace_callback($pattern, function ($matches) use (&$desc) {
            if (isset($matches[4])) {
                $img_tag_src = array_pop($matches);
                // 如果不是微信服务器上的图片
                if (strpos($img_tag_src, 'http://mmbiz.qpic.cn/') === false) {
                    $img_url = $this->getQyWeixinObject()->getMediaManager()->uploadImg($img_tag_src);
                    // $this->_opt_log->write(__METHOD__, $img_url, '上传素材');
                    if (isset($img_url['errcode'])) {
                        throw new \Exception($img_url['errmsg'], $img_url['errcode']);
                    }
                    if (empty($img_url['url'])) {
                        // var_dump($img_url);
                        // $this->_opt_log->write(__METHOD__, $img_url, '上上传图片到微信服务器失败');
                        throw new \Exception('上传图片到微信服务器失败', -101);
                    }
                    $img_url['url'] = str_replace('\/', '/', $img_url['url']);
                    $desc = str_replace($img_tag_src, $img_url['url'], $desc);
                }
            }
        }, $desc);

        return $desc;
    }
}
