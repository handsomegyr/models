<?php

namespace App\Common\Models\Qyweixin\Mysql\ExternalContact;

use App\Common\Models\Base\Mysql\Base;

class ProductAlbum extends Base
{
    /**
     * 企业微信-外部联系人管理-商品图册
     * This model is mapped to the table iqyweixin_externalcontact_product_album
     */
    public function getSource()
    {
        return 'iqyweixin_externalcontact_product_album';
    }
}
