<?php

namespace App\Common\Models\Database\Mysql\Project\Collection;

use App\Common\Models\Base\Mysql\Base;

class Structure extends Base
{

    /**
     * 数据库-数据库表结构管理
     * This model is mapped to the table idatabase_project_collection_structure
     */
    public function getSource()
    {
        return 'idatabase_project_collection_structure';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['searchable'] = $this->changeToBoolean($data['searchable']);
        $data['main'] = $this->changeToBoolean($data['main']);
        $data['required'] = $this->changeToBoolean($data['required']);
        $data['unique'] = $this->changeToBoolean($data['unique']);
        $data['export'] = $this->changeToBoolean($data['export']);
        $data['encipher'] = $this->changeToBoolean($data['encipher']);
        $data['showImage'] = $this->changeToBoolean($data['showImage']);
        $data['displayFileId'] = $this->changeToBoolean($data['displayFileId']);
        $data['isBoxSelect'] = $this->changeToBoolean($data['isBoxSelect']);
        $data['isFatherField'] = $this->changeToBoolean($data['isFatherField']);
        $data['rshKey'] = $this->changeToBoolean($data['rshKey']);
        $data['rshValue'] = $this->changeToBoolean($data['rshValue']);
        $data['isLinkageMenu'] = $this->changeToBoolean($data['isLinkageMenu']);
        $data['isQuick'] = $this->changeToBoolean($data['isQuick']);
        $data['isHive'] = $this->changeToBoolean($data['isHive']);

        return $data;
    }
}
