<?php

namespace App\Common\Models\Database;

use App\Common\Models\Base\Base;

class Log extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Database\Mysql\Log());
    }

    /**
     * 授权用户行为的跟踪日志
     */
    public function trackingLog()
    {
        $company_project_id = "";
        $project_id = "";
        $project_collection_id = "";
        $project_collection_structure_id = "";
        $plugin_id = "";
        $plugin_collection_id = "";
        $plugin_collection_structure_id = "";

        if (!empty($_REQUEST)) {
            $company_project_id = isset($_REQUEST['__COMPANY_PROJECT_ID__']) ? trim($_REQUEST['__COMPANY_PROJECT_ID__']) : '';
            $project_id = isset($_REQUEST['__PROJECT_ID__']) ? trim($_REQUEST['__PROJECT_ID__']) : '';
            $project_collection_id = isset($_REQUEST['__COLLECTION_ID__']) ? trim($_REQUEST['__COLLECTION_ID__']) : '';
            $plugin_id = isset($_REQUEST['__PLUGIN_ID__']) ? trim($_REQUEST['__PLUGIN_ID__']) : '';
            $plugin_collection_id = isset($_REQUEST['__PLUGIN_COLLECTION_ID__']) ? trim($_REQUEST['__PLUGIN_COLLECTION_ID__']) : '';
        }

        return $this->insert(array(
            'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'php cli',
            'session_info' => !empty($_SESSION) ? \\App\Common\Utils\Helper::myJsonEncode($_SESSION) : '{}',
            'post_params' => !empty($_POST) ? \\App\Common\Utils\Helper::myJsonEncode($_POST) : '{}',
            'get_params' => !empty($_GET) ? \\App\Common\Utils\Helper::myJsonEncode($_GET) : '{}',
            'server_info' => !empty($_SERVER) ? \\App\Common\Utils\Helper::myJsonEncode($_SERVER) : '{}',
            'company_project_id' => $company_project_id,
            'project_id' => $project_id,
            'project_collection_id' => $project_collection_id,
            'project_collection_structure_id' => $project_collection_structure_id,
            'plugin_id' => $plugin_id,
            'plugin_collection_id' => $plugin_collection_id,
            'plugin_collection_structure_id' => $plugin_collection_structure_id,
        ));
    }
}
