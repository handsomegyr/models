<?php

namespace App\Qyweixin\Models\Authorize;

class AuthorizeLog extends \App\Common\Models\Qyweixin\Authorize\AuthorizeLog
{

    protected $changeToArrayFields = array(
        'request_params',
        'aes_info'
    );

    /**
     * 记录日志
     *
     * @param array $info            
     * @return array()
     */
    public function record($info)
    {
        $datas = $this->getPrepareData($info);
        return $this->insert($datas);
    }

    private function getPrepareData($info)
    {
        $data = array();
        $data['SuiteId'] = isset($info['SuiteId']) ? $info['SuiteId'] : '';
        $data['CreateTime'] = isset($info['CreateTime']) ? $info['CreateTime'] : 0;
        $data['InfoType'] = isset($info['InfoType']) ? $info['InfoType'] : '';
        $data['SuiteTicket'] = isset($info['SuiteTicket']) ? $info['SuiteTicket'] : '';
        $data['AuthCorpId'] = isset($info['AuthCorpId']) ? $info['AuthCorpId'] : '';
        $data['AuthCode'] = isset($info['AuthCode']) ? $info['AuthCode'] : '';

        $data['request_params'] = isset($info['request_params']) ? \App\Common\Utils\Helper::myJsonEncode($info['request_params']) : '';
        $data['request_xml'] = isset($info['request_xml']) ? ($info['request_xml']) : '';
        $data['response'] = isset($info['response']) ? $info['response'] : '';
        $data['aes_info'] = isset($info['aes_info']) ? \App\Common\Utils\Helper::myJsonEncode($info['aes_info']) : '';
        $data['is_aes'] = isset($info['is_aes']) ? intval($info['is_aes']) : 0;
        $data['request_time'] = isset($info['request_time']) ? \App\Common\Utils\Helper::getCurrentTime($info['request_time']) : '';
        $data['response_time'] = isset($info['response_time']) ? \App\Common\Utils\Helper::getCurrentTime($info['response_time']) : '';
        $data['interval'] = isset($info['interval']) ? $info['interval'] : 0;

        $data['lock_uniqueKey'] = isset($info['lock_uniqueKey']) ? $info['lock_uniqueKey'] : '';

        return $data;
    }
}
