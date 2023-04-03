<?php

namespace App\Weixin2\Services;

class WeixinService
{
    use \App\Weixin2\Services\Traits\MediaTrait;
    use \App\Weixin2\Services\Traits\CommentTrait;
    use \App\Weixin2\Services\Traits\CustomMsgsTrait;
    use \App\Weixin2\Services\Traits\DataCubeTrait;
    use \App\Weixin2\Services\Traits\KfTrait;
    use \App\Weixin2\Services\Traits\MassMsgTrait;
    use \App\Weixin2\Services\Traits\MaterialTrait;
    use \App\Weixin2\Services\Traits\MenuTrait;
    use \App\Weixin2\Services\Traits\QrcodeTrait;
    use \App\Weixin2\Services\Traits\ReplyMsgsTrait;
    use \App\Weixin2\Services\Traits\ShorturlTrait;
    use \App\Weixin2\Services\Traits\TemplateMsgTrait;
    use \App\Weixin2\Services\Traits\UserTrait;
    use \App\Weixin2\Services\Traits\DraftTrait;
    use \App\Weixin2\Services\Traits\FreePublishTrait;

    use \App\Weixin2\Services\Traits\Miniprogram\QrcodeTrait;
    use \App\Weixin2\Services\Traits\Miniprogram\SubscribeMsgTrait;
    use \App\Weixin2\Services\Traits\Miniprogram\UrllinkTrait;
    use \App\Weixin2\Services\Traits\Miniprogram\UrlschemeTrait;

    private $authorizer_appid = "";

    private $component_appid = "";

    private $componentConfig = array();

    private $authorizerConfig = array();

    /**
     * @var \Weixin\Client
     */
    private $objWeixin = null;

    /**
     * @var \Weixin\Component
     */
    private $objWeixinComponent = null;

    /**
     * @var \App\Weixin2\Models\Component\Component
     */
    private $modelWeixinopenComponent;

    /**
     * @var \App\Weixin2\Models\Authorize\Authorizer
     */
    private $modelWeixinopenAuthorizer;

    public function __construct($authorizer_appid, $component_appid)
    {
        $this->authorizer_appid = $authorizer_appid;
        $this->component_appid = $component_appid;
        $this->modelWeixinopenComponent = new \App\Weixin2\Models\Component\Component();
        $this->modelWeixinopenAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
    }

    public function getAppConfig4Component()
    {
        $this->getToken4Component();
        return $this->componentConfig;
    }

    /**
     * @return \Weixin\Component
     */
    public function getWeixinComponent()
    {
        $this->getToken4Component();

        $this->objWeixinComponent = new \Weixin\Component($this->componentConfig['appid'], $this->componentConfig['appsecret']);
        if (!empty($this->componentConfig['access_token'])) {
            $this->objWeixinComponent->setAccessToken($this->componentConfig['access_token']);
        }
        return $this->objWeixinComponent;
    }

    public function getAppConfig4Authorizer()
    {
        $this->getToken4Authorizer();
        return $this->authorizerConfig;
    }

    /**
     * @return \Weixin\Client
     */
    public function getWeixinObject()
    {
        $this->getToken4Authorizer();

        $this->objWeixin = new \Weixin\Client();
        if (!empty($this->authorizerConfig['access_token'])) {
            $this->objWeixin->setAccessToken($this->authorizerConfig['access_token']);
        }
        return $this->objWeixin;
    }

    protected function getToken4Component()
    {
        if (empty($this->componentConfig)) {
            $this->componentConfig = $this->modelWeixinopenComponent->getTokenByAppid($this->component_appid);
            if (empty($this->componentConfig)) {
                throw new \Exception("component_appid:{$this->component_appid}所对应的记录不存在");
            }
        }
    }

    protected function getToken4Authorizer()
    {
        if (empty($this->authorizerConfig)) {
            $this->authorizerConfig = $this->modelWeixinopenAuthorizer->getTokenByAppid($this->component_appid, $this->authorizer_appid);
            if (empty($this->authorizerConfig)) {
                throw new \Exception("component_appid:{$this->component_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
        }
    }

    public function getAuthorizerInfo()
    {
        $modelAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
        $authorizerInfo = $modelAuthorizer->getInfoByAppid($this->component_appid, $this->authorizer_appid, true);
        if (empty($authorizerInfo)) {
            throw new \Exception("对应的授权方不存在");
        }
        $res = $this->getWeixinComponent()->apiGetAuthorizerInfo($this->authorizer_appid);
        $modelAuthorizer->updateAuthorizerInfo($authorizerInfo['_id'], $res, $authorizerInfo['memo']);
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
                    $img_url = $this->getWeixinObject()->getMediaManager()->uploadImg($img_tag_src);
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
