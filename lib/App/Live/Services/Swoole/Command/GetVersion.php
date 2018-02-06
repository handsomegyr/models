<?php
namespace App\Live\Services\Swoole\Command;

/**
 * 获取版本号
 *
 * @author Administrator
 *        
 */
class GetVersion extends Base
{

    public function execute(\App\Live\Services\Swoole\Server $server, array $req)
    {
        $client_id = $req['client_id'];
        $resMsg = array(
            'client_id' => $client_id,
            'cmd' => 'getVersion',
            'version' => $this->version
        );
        $server->sendJson($resMsg);
    }
}