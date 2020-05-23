<?php

namespace App\Common\Models\Database\Mysql\Plugin;

use App\Common\Models\Base\Mysql\Base;

class Collection extends Base
{

    /**
     * 数据库-插件表管理
     * This model is mapped to the table idatabase_plugin_collection
     */
    public function getSource()
    {
        return 'idatabase_plugin_collection';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['isProfessional'] = $this->changeToBoolean($data['isProfessional']);
        $data['isTree'] = $this->changeToBoolean($data['isTree']);
        $data['isRowExpander'] = $this->changeToBoolean($data['isRowExpander']);
        $data['plugin'] = $this->changeToBoolean($data['plugin']);
        $data['defaultSourceData'] = $this->changeToBoolean($data['defaultSourceData']);
        $data['isAutoHook'] = $this->changeToBoolean($data['isAutoHook']);
        $data['hook_debug_mode'] = $this->changeToBoolean($data['hook_debug_mode']);
        $data['isAllowHttpAccess'] = $this->changeToBoolean($data['isAllowHttpAccess']);
        $data['submitConfirm'] = $this->changeToBoolean($data['submitConfirm']);
        $data['hiveResultCollection'] = $this->changeToBoolean($data['hiveResultCollection']);
        $data['isEditSafe'] = $this->changeToBoolean($data['isEditSafe']);
        $data['promissionDefinition'] = $this->changeToArray($data['promissionDefinition']);

        return $data;
    }
}
